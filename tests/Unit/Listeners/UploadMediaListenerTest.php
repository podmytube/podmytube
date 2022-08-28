<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\MediaUploadedByUser;
use App\Exceptions\NotReadableFileException;
use App\Jobs\SendFileBySFTP;
use App\Listeners\UploadMediaListener;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UploadMediaListenerTest extends TestCase
{
    use RefreshDatabase;

    protected Media $media;
    protected MediaUploadedByUser $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(SendFileBySFTP::class);
        $this->media = Media::factory()->create();

        $this->event = new MediaUploadedByUser($this->media);
    }

    public function tearDown(): void
    {
        if (file_exists($this->media->uploadedFilePath())) {
            unlink($this->media->uploadedFilePath());
        }
        parent::tearDown();
    }

    /** @test */
    public function upload_media_should_fail_if_file_does_not_exists(): void
    {
        $job = new UploadMediaListener();
        $this->expectException(InvalidArgumentException::class);
        $job->handle($this->event);
    }

    /** @test */
    public function upload_media_should_fail_if_file_not_readable(): void
    {
        touch($this->media->uploadedFilePath());
        chmod($this->media->uploadedFilePath(), 0300);
        $job = new UploadMediaListener();
        $this->expectException(NotReadableFileException::class);
        $job->handle($this->event);
    }

    /** @test */
    public function upload_media_should_success(): void
    {
        touch($this->media->uploadedFilePath());
        $job = new UploadMediaListener();
        $job->handle($this->event);
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
