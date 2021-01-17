<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
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

    public function testHavingTheRightNumberOfVideos()
    {
        $expectedConsumedQuota = 7;
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
            [$factory->apikey() => $expectedConsumedQuota],
            YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed()
        );
    }

    public function testLimitingTheNumberOfResults()
    {
        $expectedConsumedQuota = 7;
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
            [$factory->apikey() => $expectedConsumedQuota],
            YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed()
        );
    }

    public function testingInvalidChannelShouldThrowException()
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeChannelVideos::forChannel('ThisChannelWillNeverExists');
    }
}
