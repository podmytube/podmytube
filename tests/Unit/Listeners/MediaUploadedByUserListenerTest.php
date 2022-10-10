<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\MediaUploadedByUserEvent;
use App\Jobs\MediaUploadedByUserJob;
use App\Jobs\UploadPodcastJob;
use App\Listeners\MediaUploadedByUserListener;
use App\Models\Channel;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MediaUploadedByUserListenerTest extends TestCase
{
    use RefreshDatabase;

    protected Media $media;
    protected Channel $channel;
    protected MediaUploadedByUserEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(UploadPodcastJob::class);
        Queue::fake(MediaUploadedByUserJob::class);
        $this->channel = $this->createChannelWithPlan();
        $this->media = Media::factory()
            ->channel($this->channel)
            ->uploadedByUser()
            ->create()
        ;

        $this->event = new MediaUploadedByUserEvent($this->media);
    }

    /** @test */
    public function media_uploaded_by_user_listener_should_work_fine(): void
    {
        touch($this->media->uploadedFilePath());
        $listener = new MediaUploadedByUserListener();
        $listener->handle($this->event);

        Queue::assertPushedOn('podwww', MediaUploadedByUserJob::class);
        Queue::assertPushed(fn (MediaUploadedByUserJob $job) => $job->media->youtube_id === $this->media->youtube_id);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $this->channel->youtube_id);
    }
}
