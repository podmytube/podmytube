<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\ApiKey;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 *
 * @coversNothing
 */
class StatusChannelCommandTest extends CommandTestCase
{
    use IsFakingYoutube;
    use RefreshDatabase;

    protected const DEFAULT_USER_ID = 1;
    protected const DEFAULT_PLAN_ID = 1;

    public function setUp(): void
    {
        parent::setUp();
        // because we need one (the 1 at least)
        ApiKey::factory()->create();
    }

    /** @test */
    public function unknown_channel_id_should_fail(): void
    {
        $this->fakeEmptyChannelResponse();
        $this->artisan('status:channel', ['channel_id' => 'unknown_channel_id'])
            ->assertExitCode(1)
        ;
    }

    /** @test */
    public function active_channel_id_should_succeed(): void
    {
        $plan = Plan::factory()->name('starter')->create();
        $channel = $this->createChannelWithPlan($plan);
        $channel->update([
            'created_at' => now()->subMonth(),
            'podcast_updated_at' => now()->subDay(),
        ]);
        $playlistId = $this->getPlaylistIdFromChannelId($channel->youtube_id);
        // faking playlist items response
        $this->fakePlaylistItemsResponse($playlistId);
        // command should run properly
        $this->artisan('status:channel', ['channel_id' => $channel->youtube_id])
            ->assertExitCode(0)
            ->expectsTable(
                ['Channel ID', 'Channel name', 'Email', 'Created', 'Updated', 'Subscription', 'Active'],
                [[
                    $channel->youtube_id,
                    $channel->channel_name,
                    $channel->user->email,
                    $channel->created_at->toDateString(),
                    $channel->podcast_updated_at->toDateString(),
                    $plan->name,
                    '✅',
                ]]
            )
        ;
    }

    /** @test */
    public function podcast_never_updated_channel_id_should_succeed(): void
    {
        $plan = Plan::factory()->name('starter')->create();
        $channel = $this->createChannelWithPlan($plan);
        $channel->update([
            'created_at' => now()->subMonth(),
            'podcast_updated_at' => null,
        ]);
        $playlistId = $this->getPlaylistIdFromChannelId($channel->youtube_id);
        // faking playlist items response
        $this->fakePlaylistItemsResponse($playlistId);
        // command should run properly
        $this->artisan('status:channel', ['channel_id' => $channel->youtube_id])
            ->assertExitCode(0)
            ->expectsTable(
                ['Channel ID', 'Channel name', 'Email', 'Created', 'Updated', 'Subscription', 'Active'],
                [[
                    $channel->youtube_id,
                    $channel->channel_name,
                    $channel->user->email,
                    $channel->created_at->toDateString(),
                    '-',
                    $plan->name,
                    '✅',
                ]]
            )
        ;
    }

    /** @test */
    public function inactive_channel_id_should_succeed(): void
    {
        $plan = Plan::factory()->isFree()->create();
        $channel = $this->createChannelWithPlan($plan);
        $channel->update([
            'created_at' => now()->subMonth(),
            'podcast_updated_at' => now()->subDay(),
            'active' => false,
        ]);

        $playlistId = $this->getPlaylistIdFromChannelId($channel->youtube_id);

        // faking playlist items response
        $this->fakePlaylistItemsResponse($playlistId);

        // command should run properly
        $this->artisan('status:channel', ['channel_id' => $channel->youtube_id])
            ->assertExitCode(0)
            ->expectsTable(
                ['Channel ID', 'Channel name', 'Email', 'Created', 'Updated', 'Subscription', 'Active'],
                [[
                    $channel->youtube_id,
                    $channel->channel_name,
                    $channel->user->email,
                    $channel->created_at->toDateString(),
                    $channel->podcast_updated_at->toDateString(),
                    $plan->name,
                    '❌',
                ]]
            )
        ;
    }
}
