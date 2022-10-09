<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendFileByRsync;
use App\Jobs\UploadPodcastJob;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UploadPodcastJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Bus::fake();
    }

    /** @test */
    public function upload_podcast_job_is_working_fine(): void
    {
        $uploadPodcastJob = new UploadPodcastJob($this->channel);
        $uploadPodcastJob->handle();

        Bus::assertDispatched(SendFileByRsync::class, 1);
        Bus::assertDispatched(
            fn (SendFileByRsync $job) => $job->localFilePath() === $uploadPodcastJob->localFilePath() &&
            $job->remoteFilePath() === $uploadPodcastJob->remoteFilePath()
        );

        // should have been update less than 1 min ago
        $this->assertNotNull($this->channel->podcast_updatedAt);
        $this->assertTrue(now()->subMinute()->lessThan($this->channel->podcast_updatedAt));
    }
}
