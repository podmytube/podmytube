<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ThumbUpdatedEvent;
use App\Exceptions\PodcastableHasNoCoverException;
use App\Jobs\SendFileByRsync;
use App\Jobs\UploadPodcastJob;
use App\Listeners\ThumbUpdatedListener;
use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class ThumbUpdatedListenerTest extends TestCase
{
    use Covers;
    use RefreshDatabase;

    protected Channel $channel;
    protected ThumbUpdatedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(UploadPodcastJob::class);
        Queue::fake(SendFileByRsync::class);
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function updated_thumb_for_channel_with_no_cover_should_throw_exception(): void
    {
        $this->event = new ThumbUpdatedEvent($this->channel);

        $this->expectException(PodcastableHasNoCoverException::class);
        $listener = new ThumbUpdatedListener();
        $listener->handle($this->event);

        Bus::assertNothingDispatched();
        Queue::assertNothingPushed();
    }

    /** @test */
    public function updated_thumb_for_playlist_with_no_cover_should_throw_exception(): void
    {
        $playlist = Playlist::factory()->channel($this->channel)->create();
        $this->event = new ThumbUpdatedEvent($playlist);

        $this->expectException(PodcastableHasNoCoverException::class);
        $listener = new ThumbUpdatedListener();
        $listener->handle($this->event);

        Bus::assertNothingDispatched();
        Queue::assertNothingPushed();
    }

    /** @test */
    public function updated_thumb_for_channel_should_dispatch_properly_on_right_queue(): void
    {
        $this->createCoverFor($this->channel);
        $this->event = new ThumbUpdatedEvent($this->channel);

        $listener = new ThumbUpdatedListener();
        $listener->handle($this->event);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $this->channel->youtube_id);

        Queue::assertPushedOn('podwww', SendFileByRsync::class);
        Queue::assertPushed(
            fn (SendFileByRsync $job) => $job->localFilePath() === $this->channel->cover->localFilePath()
                && $job->remoteFilePath() === $this->channel->cover->remoteFilePath()
        );
    }

    /** @test */
    public function updated_thumb_for_playlist_should_dispatch_properly_on_right_queue(): void
    {
        $playlist = Playlist::factory()->channel($this->channel)->create();
        $this->createCoverFor($playlist);
        $this->event = new ThumbUpdatedEvent($playlist);

        $listener = new ThumbUpdatedListener();
        $listener->handle($this->event);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $playlist->youtube_id);

        Queue::assertPushedOn('podwww', SendFileByRsync::class);
        Queue::assertPushed(
            fn (SendFileByRsync $job) => $job->localFilePath() === $playlist->cover->localFilePath()
                && $job->remoteFilePath() === $playlist->cover->remoteFilePath()
        );
    }
}
