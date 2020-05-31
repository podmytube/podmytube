<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideosTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            2,
            ($videos = new YoutubeChannelVideos())
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->videos(),
            'Expected number of videos for this channel was 2, obtained ' .
                count($videos->videos())
        );
        /**
         * obtaining channel uploads playlistid => 3
         * obtaining uploads videos => 5
         */
        $this->assertEquals(
            8,
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }
}
