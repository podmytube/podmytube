<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Channel;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class GetPlaylistsForChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unknown_channel_should_fail(): void
    {
        $this->artisan('get:playlistsForChannel', ['channel_id' => 'unknown_channel_id'])->assertExitCode(1);
    }

    /** @test */
    public function real_channel_id_should_success(): void
    {
        $this->seedApiKeys();
        $channel = Channel::factory()->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);

        // command should run properly
        $this->artisan('get:playlistsForChannel', ['channel_id' => $channel->channelId()])->assertExitCode(0);

        // once executed we should have channel's playlists in DB
        $playlists = Playlist::byChannelId($channel->channelId());
        $this->assertCount(2, $playlists);
        $this->assertTrue($playlists->contains('title', 'testPmt'));
        $this->assertTrue($playlists->contains('title', 'Favorites'));
    }
}
