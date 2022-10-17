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
        Queue::fake();
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
        Bus::fake([
            MediaUploadedByUserJob::class,
            UploadPodcastJob::class,
        ]);

        touch($this->media->uploadedFilePath());
        $listener = new MediaUploadedByUserListener();
        $listener->handle($this->event);

        Bus::assertChained([
            MediaUploadedByUserJob::class,
            UploadPodcastJob::class,
        ]);
    }
}
