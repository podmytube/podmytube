<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Channel;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadVideosByPeriodCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->starterPlan = Plan::bySlug('starter');
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
    }

    /** @test */
    public function no_channel_should_fail(): void
    {
        $this->channel->forceDelete();
        $this->artisan('download:channels')->assertExitCode(1);
    }

    /** @test */
    public function no_active_channel_should_fail(): void
    {
        $this->channel->update(['active' => 0]);
        $this->artisan('download:channels')->assertExitCode(1);
    }

    /** @test */
    public function real_channel_id_should_be_downloaded(): void
    {
        // command should run properly
        $this->artisan('download:channels')->assertExitCode(0);
    }

    /** @test */
    public function channels_with_exhausted_quota_should_be_sent_warning_mail(): void
    {
        Bus::fake(ChannelHasReachedItsLimitsJob::class);

        $expectedNumberOfMail = 3;
        for ($i = 0; $i < $expectedNumberOfMail; $i++) {
            // creating channel with starter plan
            $channel = $this->createChannelWithPlan($this->starterPlan);
            // which have reached its quota
            $this->addMediasToChannel($channel, $this->starterPlan->nb_episodes_per_month, true);
            // add a new media (that may be downloaded with a higher plan)
            $this->addMediasToChannel($channel);
        }

        $this->artisan('download:channels')->assertExitCode(0);
        Bus::assertDispatched(ChannelHasReachedItsLimitsJob::class, $expectedNumberOfMail);
    }
}
