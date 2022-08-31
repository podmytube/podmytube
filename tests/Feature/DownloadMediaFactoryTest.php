<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Factories\DownloadMediaFactory;
use App\Jobs\SendFileByRsync;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadMediaFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Channel */
    protected $channel;

    /** @var \App\Models\Media */
    protected $media;

    /** \App\Models\Subscription $subscription */
    protected $subscription;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->seedPlans();
        Bus::fake(SendFileByRsync::class);
        $this->channel = Channel::factory()->create(['channel_id' => 'test']);
        $this->subscription = Subscription::factory()->create(
            [
                'channel_id' => $this->channel->channel_id,
                'plan_id' => Plan::bySlug('forever_free')->id,
            ]
        );

        $marioCoinDownloadedFilePath = Storage::disk('tmp')->path(self::MARIO_COIN_VIDEO . '.mp3');
        if (file_exists($marioCoinDownloadedFilePath)) {
            unlink($marioCoinDownloadedFilePath);
        }
    }

    /** YT media does not exists - should throw exception.
     * @test
     */
    public function invalid_media_should_be_rejected(): void
    {
        $this->media = Media::factory()->create([
            'channel_id' => $this->channel->channel_id,
            'media_id' => 'absolutely-not-valid',
            'title' => null,
            'description' => null,
        ]);
        DownloadMediaFactory::media($this->media)->run();
        $this->assertEquals(Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE, $this->media->status);
        $this->assertNull($this->media->title);
        $this->assertNull($this->media->description);

        $this->someChecksWhenNotDowloaded();
    }

    /** @test */
    public function episode_should_not_being_grabbed_because_too_old(): void
    {
        // channel accept only episode from now
        $this->channel->update(['reject_video_too_old' => now()]);

        $this->media = Media::factory()->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
            ]
        );
        DownloadMediaFactory::media($this->media)->run();
        $this->assertEquals(Media::STATUS_AGE_FILTERED, $this->media->status);

        $this->someChecksWhenNotDowloaded();
    }

    /** @test */
    public function video_is_being_downloaded(): void
    {
        // downloaded file may have various size once transformed in audio
        $expectedMediaLength = [26666, 26898, 27429];
        $expectedDuration = 5;
        $this->media = Media::factory()->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_COIN_VIDEO,
            ]
        );
        $this->assertTrue(DownloadMediaFactory::media($this->media)->run(), 'channel video should have been processed');
        $this->media = Media::byMediaId(self::MARIO_COIN_VIDEO);
        $this->assertNotNull($this->media);
        $this->assertEquals('Super Mario Bros. - Coin Sound Effect', $this->media->title);
        // same video is giving me 2 length 26898 && 26666 depends of lib/python installed
        $this->assertTrue(in_array($this->media->length, $expectedMediaLength));
        $this->assertEquals($expectedDuration, $this->media->duration);
        $this->assertEquals(Media::STATUS_DOWNLOADED, $this->media->status);
        $this->assertEquals(5, $this->media->duration);
        Bus::assertDispatched(SendFileByRsync::class);
    }

    public function someChecksWhenNotDowloaded(): void
    {
        $this->assertNull($this->media->grabbed_at);
        $this->assertEquals(0, $this->media->length);
        $this->assertEquals(0, $this->media->duration);
    }

    /**
     * @test
     * some medias may be missing on remote mp3 server although present in DB.
     * If present in DB I should have them on the remote server.
     */
    public function force_download_should_be_ok(): void
    {
        // adding grabbed media(s) to channel (with free plan)
        $this->media = Media::factory()
            ->grabbedAt(now())
            ->create(
                [
                    'channel_id' => $this->channel->channel_id,
                    'media_id' => self::MARIO_COIN_VIDEO,
                ]
            )
        ;

        DownloadMediaFactory::media($this->media, true)->run();
        $this->assertEquals(Media::STATUS_DOWNLOADED, $this->media->status);
    }
}
