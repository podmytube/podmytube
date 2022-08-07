<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\LineLogIsEmptyException;
use App\Exceptions\LineLogIsInvalidException;
use App\Factories\LineLogParserFactory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LineLogParserFactoryTest extends TestCase
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
        $lineLogParser = LineLogParserFactory::read($logData['logLine'])->parse();
        $this->assertNotNull($lineLogParser);
        $this->assertInstanceOf(LineLogParserFactory::class, $lineLogParser);
        $this->assertEquals($logData['method'], $lineLogParser->method());
        $this->assertEquals($logData['query'], $lineLogParser->query());
        $this->assertEquals($logData['status'], $lineLogParser->status());
        if ($logData['status'] === 404) {
            $this->assertFalse($lineLogParser->isSuccessful());
        } else {
            $this->assertTrue($lineLogParser->isSuccessful());
        }

        if ($logData['weight'] === null) {
            $this->assertNull($lineLogParser->weight());
        } else {
            $this->assertEquals($logData['weight'], $lineLogParser->weight());
        }

        $this->assertNotNull($lineLogParser->logDate());
        $this->assertInstanceOf(Carbon::class, $lineLogParser->logDate());
        $this->assertEquals($logData['date'], $lineLogParser->logDate()->format('Y-m-d H:i:s'));
        if ($logData['channel_id'] === null) {
            $this->assertNull($lineLogParser->channelId());
            $this->assertNull($lineLogParser->mediaId());
        } else {
            $this->assertEquals($logData['channel_id'], $lineLogParser->channelId());
            $this->assertEquals($logData['media_id'], $lineLogParser->mediaId());
        }
    }

    /**
     * @test
     * @dataProvider not_successful_line_provider
     */
    public function parse_should_throw_exception(array $logData, string $expectedException): void
    {
        $this->expectException($expectedException);
        LineLogParserFactory::read($logData['logLine'])->parse();
    }

    /**
     * ===================================================================
     * Helpers and methods
     * ===================================================================.
     */
    public function not_successful_line_provider()
    {
        return [
            'logline is null' => [['logLine' => null], LineLogIsEmptyException::class],
            'logline is empty string' => [['logLine' => ''], LineLogIsEmptyException::class],
            'logline is nonsense' => [['logLine' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'], LineLogIsInvalidException::class],
        ];
    }

    public function successful_line_provider()
    {
        return [
            'logline for root' => [
                [
                    'logLine' => '172.18.0.5 - - [01/Feb/2020:10:01:09 +0200] "GET / HTTP/1.1" 200 2',
                    'method' => 'GET',
                    'query' => '/',
                    'status' => 200,
                    'date' => '2020-02-01 10:01:09',
                    'channel_id' => null,
                    'media_id' => null,
                    'weight' => 2,
                ],
            ],
            'logline for robots.txt' => [
                [
                    'logLine' => '172.18.0.5 - - [29/Mar/2021:16:03:18 +0200] "GET /robots.txt HTTP/1.1" 404 196',
                    'method' => 'GET',
                    'query' => '/robots.txt',
                    'status' => 404,
                    'date' => '2021-03-29 16:03:18',
                    'channel_id' => null,
                    'media_id' => null,
                    'weight' => 196,
                ],
            ],
            'logline for mp3 with http 206 7Mo downloaded' => [
                [
                    'logLine' => '172.18.0.5 - - [15/Nov/2020:04:59:33 +0200] "GET /UCaUOAMfxMyI6PHQ6t3yeYpQ/pmS9Rv_dxyE.mp3 HTTP/1.1" 206 7774245',
                    'method' => 'GET',
                    'query' => '/UCaUOAMfxMyI6PHQ6t3yeYpQ/pmS9Rv_dxyE.mp3',
                    'status' => 206,
                    'date' => '2020-11-15 04:59:33',
                    'channel_id' => 'UCaUOAMfxMyI6PHQ6t3yeYpQ',
                    'media_id' => 'pmS9Rv_dxyE',
                    'weight' => 7774245,
                ],
            ],
            'logline for mp3 with http 200 51Mo downloaded' => [
                [
                    'logLine' => '172.18.0.5 - - [09/Aug/2021:16:17:47 +0200] "GET /UCRU38zigLJNtMIh7oRm2hIg/-z42SnrmZgs.mp3 HTTP/1.1" 200 51724605',
                    'method' => 'GET',
                    'query' => '/UCRU38zigLJNtMIh7oRm2hIg/-z42SnrmZgs.mp3',
                    'status' => 200,
                    'date' => '2021-08-09 16:17:47',
                    'channel_id' => 'UCRU38zigLJNtMIh7oRm2hIg',
                    'media_id' => '-z42SnrmZgs',
                    'weight' => 51724605,
                ],
            ],
            'logline for mp3 with http 200 nothing downloaded' => [
                [
                    'logLine' => '172.18.0.5 - - [09/Aug/2021:16:51:09 +0200] "HEAD /UCSMzy1n4Arqk_hCCOYOQn9g/B9BHzMWIYLI.mp3 HTTP/1.1" 200 -',
                    'method' => 'HEAD',
                    'query' => '/UCSMzy1n4Arqk_hCCOYOQn9g/B9BHzMWIYLI.mp3',
                    'status' => 200,
                    'date' => '2021-08-09 16:51:09',
                    'channel_id' => 'UCSMzy1n4Arqk_hCCOYOQn9g',
                    'media_id' => 'B9BHzMWIYLI',
                    'weight' => null,
                ],
            ],
        ];
    }
}
