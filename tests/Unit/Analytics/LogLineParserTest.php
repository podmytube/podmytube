<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Analytics\LogLineParser;
use App\Exceptions\LogLineIsEmptyException;
use App\Exceptions\LogLineIsInvalidException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LogLineParserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider successful_line_provider
     */
    public function parse_is_fine(array $logData): void
    {
        $LoglineParser = LogLineParser::read($logData['logLine'])->parse();
        $this->assertNotNull($LoglineParser);
        $this->assertInstanceOf(LogLineParser::class, $LoglineParser);

        // we should get proper informations aboyt query
        $this->assertEquals($logData['method'], $LoglineParser->method());
        $this->assertEquals($logData['query'], $LoglineParser->query());

        // status should be set
        $this->assertEquals($logData['status'], $LoglineParser->status());
        if ($logData['status'] === 404) {
            $this->assertFalse($LoglineParser->isSuccessful());
        } else {
            $this->assertTrue($LoglineParser->isSuccessful());
        }

        // weight should be filled
        if ($logData['weight'] === null) {
            $this->assertNull($LoglineParser->weight());
        } else {
            $this->assertEquals($logData['weight'], $LoglineParser->weight());
        }

        // date should be a Carbon instance
        $this->assertNotNull($LoglineParser->logDate());
        $this->assertInstanceOf(Carbon::class, $LoglineParser->logDate());
        $this->assertEquals($logData['date'], $LoglineParser->logDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($logData['day'], $LoglineParser->logDay());

        // channel_id & media_id should be set (according to dataset)
        if ($logData['channel_id'] === null) {
            $this->assertNull($LoglineParser->channelId());
            $this->assertNull($LoglineParser->mediaId());
        } else {
            $this->assertEquals($logData['channel_id'], $LoglineParser->channelId());
            $this->assertEquals($logData['media_id'], $LoglineParser->mediaId());
        }
    }

    /**
     * @test
     * @dataProvider not_successful_line_provider
     */
    public function parse_should_throw_exception(array $logData, string $expectedException): void
    {
        $this->expectException($expectedException);
        LogLineParser::read($logData['logLine'])->parse();
    }

    /**
     * ===================================================================
     * Helpers and methods
     * ===================================================================.
     */
    public function not_successful_line_provider()
    {
        return [
            'logline is null' => [['logLine' => null], LogLineIsEmptyException::class],
            'logline is empty string' => [['logLine' => ''], LogLineIsEmptyException::class],
            'logline is nonsense' => [['logLine' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'], LogLineIsInvalidException::class],
        ];
    }

    public function successful_line_provider()
    {
        return [
            'document root status 200' => [
                [
                    'logLine' => '{"log":"172.18.0.4 - - [06/Aug/2022:18:32:40 +0200] \"GET / HTTP/1.1\" 200 2 \"-\" \"Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.79 Mobile Safari/537.36\" \"3.238.76.83, 172.70.175.193\"\n","stream":"stdout","time":"2022-08-06T16:32:40.208637699Z"}',
                    'method' => 'GET',
                    'query' => '/',
                    'status' => 200,
                    'date' => '2022-08-06 18:32:40',
                    'day' => '2022-08-06',
                    'channel_id' => null,
                    'media_id' => null,
                    'weight' => 2,
                ],
            ],
            'robots.txt not found status 404' => [
                [
                    'logLine' => '{"log":"172.18.0.4 - - [06/Aug/2022:00:18:49 +0200] \"GET /robots.txt HTTP/1.1\" 404 153 \"-\" \"Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)\" \"54.36.149.56, 141.101.68.100\"\n","stream":"stdout","time":"2022-08-05T22:18:49.423635566Z"}',
                    'method' => 'GET',
                    'query' => '/robots.txt',
                    'status' => 404,
                    'date' => '2022-08-06 00:18:49',
                    'day' => '2022-08-06',
                    'channel_id' => null,
                    'media_id' => null,
                    'weight' => 153,
                ],
            ],
            'media file successfully downloaded status 200' => [
                [
                    'logLine' => '{"log":"172.18.0.4 - - [06/Aug/2022:14:48:59 +0200] \"GET /UCMnHkvrh_1fMWTJA_ru9ATQ/u_MpB2A39S0.mp3 HTTP/1.1\" 200 2211472 \"-\" \"podnods-crawler\" \"18.119.103.206, 172.70.131.57\"\n","stream":"stdout","time":"2022-08-06T12:48:59.041675774Z"}',
                    'method' => 'GET',
                    'query' => '/UCMnHkvrh_1fMWTJA_ru9ATQ/u_MpB2A39S0.mp3',
                    'status' => 200,
                    'date' => '2022-08-06 14:48:59',
                    'day' => '2022-08-06',
                    'channel_id' => 'UCMnHkvrh_1fMWTJA_ru9ATQ',
                    'media_id' => 'u_MpB2A39S0',
                    'weight' => 2211472,
                ],
            ],
            'not updated media file status 304' => [
                [
                    'logLine' => '{"log":"172.18.0.4 - - [06/Aug/2022:23:59:44 +0200] \"GET /UCSMzy1n4Arqk_hCCOYOQn9g/bbleNcW2ub8.mp3 HTTP/1.1\" 304 0 \"-\" \"Libsyn4-peek\" \"204.16.243.139, 108.162.241.132\"\n","stream":"stdout","time":"2022-08-06T21:59:44.228601652Z"}',
                    'method' => 'GET',
                    'query' => '/UCSMzy1n4Arqk_hCCOYOQn9g/bbleNcW2ub8.mp3',
                    'status' => 304,
                    'date' => '2022-08-06 23:59:44',
                    'day' => '2022-08-06',
                    'channel_id' => 'UCSMzy1n4Arqk_hCCOYOQn9g',
                    'media_id' => 'bbleNcW2ub8',
                    'weight' => 0,
                ],
            ],
            'media file not found status 404' => [
                [
                    'logLine' => '{"log":"172.18.0.4 - - [06/Aug/2022:17:16:32 +0200] \"GET /UCRU38zigLJNtMIh7oRm2hIg/z8gqSeShfjQ.mp3 HTTP/1.1\" 404 153 \"-\" \"Go-http-client/2.0\" \"82.212.151.200, 162.158.233.92\"\n","stream":"stdout","time":"2022-08-06T15:16:32.646265551Z"}',
                    'method' => 'GET',
                    'query' => '/UCRU38zigLJNtMIh7oRm2hIg/z8gqSeShfjQ.mp3',
                    'status' => 404,
                    'date' => '2022-08-06 17:16:32',
                    'day' => '2022-08-06',
                    'channel_id' => 'UCRU38zigLJNtMIh7oRm2hIg',
                    'media_id' => 'z8gqSeShfjQ',
                    'weight' => 153,
                ],
            ],
        ];
    }
}
