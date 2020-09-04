<?php

namespace Tests\Unit\Youtube;

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
        ($videos = new YoutubeChannelVideos())
            ->forChannel('UCq80IvL314jsE7PgYsTdw7Q')
            ->videos();

        $this->assertCount(0, $videos->videos());
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            2,
            ($videos = new YoutubeChannelVideos())
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, 50)
                ->videos(),
            'Expected number of videos for this channel was 2, obtained ' .
                count($videos->videos())
        );
        /**
         * quota usage
         * obtaining channel uploads playlistid => 3 -- cheated so 0
         * obtaining videos list for uploads => 5
         */
        $this->assertEqualsCanonicalizing(
            [$videos->apikey() => 5],
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }

    public function testLimitingTheNumberOfResults()
    {
        $this->assertCount(
            50,
            ($videos = new YoutubeChannelVideos())
                ->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID, 50)
                ->videos(),
            'Expected number of videos for this channel was 2, obtained ' .
                count($videos->videos())
        );

        /**
         * quota usage
         * obtaining channel uploads playlistid => 3 -- cheated so 0
         * obtaining videos list for uploads => 5
         */
        $this->assertEqualsCanonicalizing(
            [$videos->apikey() => 5],
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }
}
