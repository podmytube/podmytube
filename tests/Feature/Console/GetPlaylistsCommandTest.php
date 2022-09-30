<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\ApiKey;
use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 *
 * @coversNothing
 */
class GetPlaylistsCommandTest extends TestCase
{
    use IsFakingYoutube;
    use RefreshDatabase;

    protected Channel $channel;
    protected string $playlistId;

    public function setUp(): void
    {
        parent::setUp();
        // because we need one (the 1 at least)
        ApiKey::factory()->create();

        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->playlistId = $this->getPlaylistIdFromChannelId($this->channel->youtube_id);
    }

    /** @test */
    public function no_active_channel_should_fail(): void
    {
        $this->channel->update(['active' => false]);
        $this->artisan('get:playlists')
            ->assertExitCode(1)
        ;
    }

    /** @test */
    public function unknown_channel_should_fail(): void
    {
        $this->artisan('get:playlists', ['channel_id' => 'unknown_channel_id'])
            ->assertExitCode(1)
        ;
    }

    /** @test */
    public function get_specific_channel_playlists_should_succeed(): void
    {
        $this->fakePlaylistResponse($this->channel->youtube_id);

        $this->assertCount(0, $this->channel->playlists);
        $this->artisan('get:playlists', ['channel_id' => $this->channel->youtube_id])
            ->assertExitCode(0)
        ;
        $this->channel->refresh();

        // channel should have playlists (from tests/Fixtures/Youtube/playlists-response.json)
        $expectedPlaylists = [
            'FLw6bU9JT_Lihb2pbtqAUGQw1' => 'Lorem ipsum dolore sit amet',
            'FLw6bU9JT_Lihb2pbtqAUGQw2' => 'Consectetur adipiscing elit',
        ];

        $this->assertCount(count($expectedPlaylists), $this->channel->playlists);
        $this->assertEqualsCanonicalizing(
            $expectedPlaylists,
            $this->channel->playlists->pluck('title', 'youtube_playlist_id')->toArray()
        );
    }
}
