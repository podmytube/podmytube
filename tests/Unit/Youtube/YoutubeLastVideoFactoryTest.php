<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Factories\YoutubeLastVideoFactory;
use App\Models\Quota;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeLastVideoFactoryTest extends YoutubeTestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_last_video_should_be_good(): void
    {
        $expectedQuotaUsed = 12;

        /*
         * this factory is getting the last channel media info+tags,
         * then it is storing the total quota consumption.
         */
        $factory = YoutubeLastVideoFactory::forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID);
        $lastMedia = $factory->lastMedia();

        // checking results
        $this->assertEquals(self::BEACH_VOLLEY_VIDEO_1, $lastMedia['media_id']);
        $this->assertEqualsCanonicalizing(['dev', 'podmytube'], $lastMedia['tags']);

        /** checking quota consumption has been persisted */
        $quotaModel = Quota::first();
        $this->assertEquals(
            $expectedQuotaUsed,
            $quotaModel->quota_used,
            "We were expecting to consume {$expectedQuotaUsed}, and we consumed {$quotaModel->quota_used}"
        );
        $this->assertEquals(YoutubeLastVideoFactory::SCRIPT_NAME, $quotaModel->script);
    }

    /** @test */
    public function getting_invalid_media_should_fail(): void
    {
        $this->expectException(YoutubeGenericErrorException::class);
        YoutubeLastVideoFactory::forChannel('ChannelWhichWillNeverExists');
    }
}
