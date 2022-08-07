<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DownloadVideosByChannelCommandTest extends TestCase
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
    public function unknown_channel_should_fail(): void
    {
        $this->artisan('download:channel', ['channel_id' => 'unknown_channel_id'])->assertExitCode(1);
    }

    /** @test */
    public function real_channel_id_should_be_downloaded(): void
    {
        // command should run properly
        $this->artisan('download:channel', ['channel_id' => $this->channel->channelId()])->assertExitCode(0);
    }
}
