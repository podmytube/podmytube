<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Exceptions\NotReadableFileException;
use App\Exceptions\UploadedMediaByUserIsMissingException;
use App\Jobs\SendFileByRsync;
use App\Jobs\TransferMediaUploadedByUserJob;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TransferMediaUploadedByUserJobTest extends TestCase
{
    use RefreshDatabase;

    protected Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->media = Media::factory()
            ->uploadedByUser()
            ->create()
        ;
    }

    public function tearDown(): void
    {
        if (file_exists($this->media->uploadedFilePath())) {
            unlink($this->media->uploadedFilePath());
        }
        parent::tearDown();
    }

    /** @test */
    public function missing_uploaded_file_for_media_should_throw_exception(): void
    {
        $job = new TransferMediaUploadedByUserJob($this->media);
        $this->expectException(UploadedMediaByUserIsMissingException::class);
        $job->handle();
    }

    /** @test */
    public function not_readable_media_should_throw_exception(): void
    {
        touch($this->media->uploadedFilePath());
        chmod($this->media->uploadedFilePath(), 0300);

        $job = new TransferMediaUploadedByUserJob($this->media);
        $this->expectException(NotReadableFileException::class);
        $job->handle();
    }

    /** @test */
    public function successfully_uploaded_file_should_dispatch_transfer(): void
    {
        touch($this->media->uploadedFilePath());

        Bus::fake(SendFileByRsync::class);

        $job = new TransferMediaUploadedByUserJob($this->media);
        $job->handle();

        Bus::assertDispatched(SendFileByRsync::class, 1);
        Bus::assertDispatched(
            fn (SendFileByRsync $job) => $job->localFilePath === $this->media->uploadedFilePath()
            && $job->remoteFilePath === $this->media->remoteFilePath()
        );
    }
}
