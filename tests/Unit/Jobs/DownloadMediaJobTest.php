<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DownloadMediaJob;
use App\Jobs\SendFileBySFTP;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadMediaJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->seedPlans();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);

        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function download_media_job_is_fine(): void
    {
        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_MUSHROOM_VIDEO,
            ]
        );
        $job = new DownloadMediaJob($this->media);

        $this->assertNotNull($job);
        $this->assertInstanceOf(DownloadMediaJob::class, $job);

        $job->handle();
        Storage::disk(SendFileBySFTP::REMOTE_DISK)->assertExists($this->media->remoteFilePath());
    }

    /** @test */
    public function force_download_media_job_is_fine(): void
    {
        // file is already grabbed
        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_MUSHROOM_VIDEO,
                'grabbed_at' => now(),
            ]
        );
        $job = new DownloadMediaJob($this->media, true);

        $this->assertNotNull($job);
        $this->assertInstanceOf(DownloadMediaJob::class, $job);

        $job->handle();
        Storage::disk(SendFileBySFTP::REMOTE_DISK)->assertExists($this->media->remoteFilePath());
    }
}
