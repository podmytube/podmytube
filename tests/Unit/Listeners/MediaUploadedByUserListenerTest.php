<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\MediaUploadedByUserEvent;
use App\Jobs\SendFileByRsync;
use App\Jobs\TransferMediaUploadedByUserJob;
use App\Listeners\MediaUploadedByUserListener;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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
    protected MediaUploadedByUserEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(SendFileByRsync::class);
        $this->media = Media::factory()->uploadedByUser()->create();

        $this->event = new MediaUploadedByUserEvent($this->media);
    }

    /** @test */
    public function uploaded_media_should_dispatch_transfer(): void
    {
        Bus::fake();

        touch($this->media->uploadedFilePath());
        $job = new MediaUploadedByUserListener();
        $job->handle($this->event);
        Bus::assertDispatched(TransferMediaUploadedByUserJob::class);
    }
}
