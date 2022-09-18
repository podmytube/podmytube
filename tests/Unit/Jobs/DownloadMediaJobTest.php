<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DownloadMediaJob;
use App\Jobs\SendFileByRsync;
use App\Models\Channel;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DownloadMediaJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        Bus::fake(SendFileByRsync::class);

        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function download_media_job_is_fine(): void
    {
        $this->media = Media::factory()->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::MARIO_MUSHROOM_VIDEO,
            ]
        );
        $job = new DownloadMediaJob($this->media);

        $this->assertNotNull($job);
        $this->assertInstanceOf(DownloadMediaJob::class, $job);

        $job->handle();
        Bus::assertDispatched(SendFileByRsync::class);
    }

    /** @test */
    public function force_download_media_job_is_fine(): void
    {
        // file is already grabbed
        $this->media = Media::factory()
            ->grabbedAt(now())
            ->create(
                [
                    'channel_id' => $this->channel->channel_id,
                    'media_id' => self::MARIO_MUSHROOM_VIDEO,
                ]
            )
        ;
        $job = new DownloadMediaJob($this->media, true);

        $this->assertNotNull($job);
        $this->assertInstanceOf(DownloadMediaJob::class, $job);

        $job->handle();
        Bus::assertDispatched(SendFileByRsync::class);
    }
}
