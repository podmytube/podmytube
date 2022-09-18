<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
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
        $this->starterPlan = Plan::factory()->name('starter')->create();
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
}
