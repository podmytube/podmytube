<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Factories\YoutubeLastVideoFactory;
use App\Quota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeLastVideoFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }

    public function test_getting_last_video_should_be_good(): void
    {
        $expectedQuotaUsed = 12;
        /**
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

    public function test_channel_with_no_videos_should_throw_exception(): void
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeLastVideoFactory::forChannel('UCq80IvL314jsE7PgYsTdw7Q'); // accropolis replays (strangely)
    }

    public function test_getting_invalid_media_should_fail(): void
    {
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeLastVideoFactory::forChannel('ChannelWhichWillNeverExists');
    }
}
