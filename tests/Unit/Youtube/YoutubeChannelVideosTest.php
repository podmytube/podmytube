<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeQueryFailureException;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelVideosTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    /**
     * stragely this channel (accropolis replays has no videos)
     * it displays some but when you click on the videos tab you get
     * "This channel has no videos."
     */
    public function testChannelWithNoVideos()
    {
        $this->assertCount(0, YoutubeChannelVideos::forChannel('UCq80IvL314jsE7PgYsTdw7Q')->videos());
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $factory = YoutubeChannelVideos::forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, 50);
        $this->assertCount(
            2,
            $videos = $factory->videos(),
            'Expected number of videos for this channel was 2, obtained ' . count($videos)
        );
        /**
         * quota usage
         * obtaining channel uploads playlistid => 3 -- cheated so 0
         * obtaining videos list for uploads => 5
         */
        $this->assertEqualsCanonicalizing(
            [$factory->apikey() => 5],
            YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed()
        );
    }

    public function testLimitingTheNumberOfResults()
    {
        $factory = YoutubeChannelVideos::forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID, 50);

        $this->assertCount(
            50,
            $videos = $factory->videos(),
            'Expected number of videos for this channel was 2, obtained ' .
                count($videos)
        );

        /**
         * quota usage
         * obtaining channel uploads playlistid => 3 -- cheated so 0
         * obtaining videos list for uploads => 5
         */
        $this->assertEqualsCanonicalizing(
            [$factory->apikey() => 5],
            YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed()
        );
    }

    public function testingInvalidChannelShouldThrowException()
    {
        $this->expectException(YoutubeQueryFailureException::class);
        YoutubeChannelVideos::forChannel('ThisChannelWillNeverExists');
    }
}
