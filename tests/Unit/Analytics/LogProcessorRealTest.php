<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Analytics\LogProcessor;
use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */

/**
 * @internal
 * @coversNothing
 */
class LogProcessorRealTest extends TestCase
{
    use RefreshDatabase;

    protected array $channelsWithMedias = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->mp3SampleLogFile = $this->fixturesPath('Logs/mp3-sample.log');
        $this->createChannelsAndMedia();
    }

    /** @test */
    public function it_should_process_all_lines_from_file(): void
    {
        // code
        $logProcessor = LogProcessor::withFile($this->mp3SampleLogFile)->process();

        $expectedNumberOfProcessedLines = count(file($this->mp3SampleLogFile)); // number of line in sample
        $expectedNumberOfValidLines = $expectedNumberOfProcessedLines - 14;
        $expectedNumberOfMediasNotFound = 5;

        /*
        |--------------------------------------------------------------------------
        | total number of lines
        |--------------------------------------------------------------------------
        */
        $this->assertEquals(
            $expectedNumberOfProcessedLines,
            $logProcessor->nbProcessedLines(),
            'expected processed lines ' . $expectedNumberOfProcessedLines . ' - obtained ' . $logProcessor->nbProcessedLines()
        );

        /*
        |--------------------------------------------------------------------------
        | total number of VALID lines
        |--------------------------------------------------------------------------
        */
        $this->assertEquals(
            $expectedNumberOfValidLines,
            $logProcessor->nbValidLines(),
            'expected valid lines ' . $expectedNumberOfValidLines . ' - obtained ' . $logProcessor->nbValidLines()
        );

        /*
        |--------------------------------------------------------------------------
        | total number of medias not found
        |--------------------------------------------------------------------------
        */
        $this->assertEquals(
            $expectedNumberOfMediasNotFound,
            $logProcessor->nbMediasNotFound(),
            'expected medias not found lines ' . $expectedNumberOfMediasNotFound . ' - obtained ' . $logProcessor->nbMediasNotFound()
        );

        /*
        |--------------------------------------------------------------------------
        | downloads checking
        |--------------------------------------------------------------------------
        */
        array_map(function (string $channelId, array $mediasInfos) use ($logProcessor): void {
            if ($channelId === 'DELETED_CHANNEL') {
                // not known channel, we should have no downloads counted on it
                return;
            }

            $channel = Channel::byChannelId($channelId);
            $nbDownloadsByChannel = 0;
            array_map(
                function ($mediaInfos) use (&$nbDownloadsByChannel, $logProcessor): void {
                    $media = Media::byMediaId($mediaInfos['media_id']);

                    $counted = $logProcessor->nbDownloadsByMedia($media, Carbon::create(2022, 8, 6));
                    $this->assertEquals(
                        $mediaInfos['nb_downloads'],
                        $counted,
                        'expecting ' . $mediaInfos['nb_downloads'] . ' downloads for ' . $mediaInfos['media_id'] . ' - obtained ' . $counted
                    );
                    $nbDownloadsByChannel += $mediaInfos['nb_downloads'];
                },
                $mediasInfos
            );
            $this->assertEquals(
                $nbDownloadsByChannel,
                $logProcessor->nbDownloadsByChannel(
                    $channel,
                    Carbon::create(2022, 8, 6)
                )
            );
        }, array_keys($this->channelsWithMedias), $this->channelsWithMedias);

        // Added 2 extra lines with another download day should be stored !
        $this->assertEquals(2, Download::forChannelThisDay(
            channel: Channel::byChannelId('ANOTHER_CHANNEL'),
            date: Carbon::create(2022, 9, 27)
        ));
        $this->assertEquals(2, Download::forMediaThisDay(
            media: Media::byMediaId('5AVuC8_zDUU'),
            date: Carbon::create(2022, 9, 27)
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    protected function createChannelsAndMedia(): void
    {
        $this->channelsWithMedias = [
            'KNOWN_CHANNEL' => [
                [
                    'media_id' => 'kRFZSlYSugg',
                    'length' => 10788499,
                    'grabbed_at' => now(),
                    'nb_downloads' => 17,
                ],
                [
                    'media_id' => 'V7GPZPbJDIE',
                    'length' => 778953,
                    'grabbed_at' => now(),
                    'nb_downloads' => 26,
                ],
            ],
            'ANOTHER_CHANNEL' => [
                [
                    'media_id' => '5AVuC8_zDUU', // 404, 304 or partial downloads
                    'length' => 10000,
                    'grabbed_at' => now(),
                    'nb_downloads' => 0,
                ],
            ],
            'DELETED_CHANNEL' => [
                [ // this one is 404
                    'media_id' => 'iT1eQ52lNy4',
                    'length' => 50000,
                    'grabbed_at' => now(),
                    'nb_downloads' => 0,
                ],
            ],
        ];
        array_map(function (string $channelId, array $mediasInfo): void {
            if ($channelId === 'DELETED_CHANNEL') {
                return;
            }
            $channel = Channel::factory()
                ->create(['channel_id' => $channelId])
            ;

            array_map(function ($mediaInfo) use ($channel): void {
                Media::factory()
                    ->channel($channel)
                    ->create([
                        'media_id' => $mediaInfo['media_id'],
                        'length' => $mediaInfo['length'],
                        'grabbed_at' => $mediaInfo['grabbed_at'],
                    ])
                ;
            }, $mediasInfo);
        }, array_keys($this->channelsWithMedias), $this->channelsWithMedias);
    }
}
