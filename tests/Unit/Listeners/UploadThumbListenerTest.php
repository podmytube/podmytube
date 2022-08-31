<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ThumbUpdated;
use App\Exceptions\NotReadableFileException;
use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\SendFileByRsync;
use App\Listeners\UploadThumbListener;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UploadThumbListenerTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected UploadThumbListener $job;
    protected InteractsWithPodcastable $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(SendFileByRsync::class);
        $this->job = new UploadThumbListener();
        $this->channel = $this->createChannelWithPlan();
        $this->createCoverFor($this->channel);

        $this->event = new ThumbUpdated($this->channel);
    }

    public function tearDown(): void
    {
        if (file_exists($this->channel->cover->localFilePath())) {
            unlink($this->channel->cover->localFilePath());
        }
        parent::tearDown();
    }

    /** @test */
    public function upload_media_should_fail_if_file_does_not_exists(): void
    {
        unlink($this->channel->cover->localFilePath());
        $this->expectException(InvalidArgumentException::class);
        $this->job->handle($this->event);
    }

    /** @test */
    public function upload_thumb_should_fail_if_file_not_readable(): void
    {
        chmod($this->channel->cover->localFilePath(), 0300);
        $this->expectException(NotReadableFileException::class);
        $this->job->handle($this->event);
    }

    /** @test */
    public function upload_media_should_success(): void
    {
        $this->job->handle($this->event);
        Bus::assertDispatched(SendFileByRsync::class);
    }
}
