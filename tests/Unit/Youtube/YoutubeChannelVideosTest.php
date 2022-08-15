<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeChannelVideosTest extends YoutubeTestCase
{
    use RefreshDatabase;

    /** @test */
    public function having_the_right_number_of_videos(): void
    {
        $expectedConsumedQuota = 5;
        $factory = YoutubeChannelVideos::forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, 50);
        $this->assertCount(
            2,
            $videos = $factory->videos(),
            'Expected number of videos for this channel was 2, obtained ' . count($videos)
        );
        /*
         * quota usage
         * obtaining channel uploads playlistid => 3 -- cheated so 0
         * obtaining videos list for uploads => 5
         */
        $this->assertEqualsCanonicalizing(
            [$factory->apikey() => $expectedConsumedQuota],
            YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed()
        );
    }

    /** @test */
    public function grabbing_max_results_videos_once_should_be_ok(): void
    {
        /**
         * actually when querying youtube I ask for maxresults (50)
         * limit is only useful to stop querying youtube.
         * Imagine you want to grab all Pewdiepie videos ...
         */
        $expectedNumberOfVideos = 50;
        $factory = YoutubeChannelVideos::forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID, 3);
        $nbVideos = count($factory->videos());
        $this->assertEquals(
            $expectedNumberOfVideos,
            $nbVideos,
            "Expected number of videos for this channel was {$expectedNumberOfVideos}, obtained {$nbVideos}"
        );
    }

    /** @test */
    public function grabbing_max_results_videos_many_times_should_be_ok(): void
    {
        $expectedNumberOfVideos = 150;
        $factory = YoutubeChannelVideos::forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID, $expectedNumberOfVideos);
        $nbVideos = count($factory->videos());
        $this->assertEquals(
            $expectedNumberOfVideos,
            $nbVideos,
            "Expected number of videos for this channel was {$expectedNumberOfVideos}, obtained {$nbVideos}"
        );
    }

    /** @test */
    public function invalid_channel_should_throw_exception(): void
    {
        $this->expectException(YoutubeGenericErrorException::class);
        YoutubeChannelVideos::forChannel('ThisChannelWillNeverExists');
    }
}
