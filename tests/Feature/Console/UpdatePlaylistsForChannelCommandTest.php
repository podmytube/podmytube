<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Exceptions\PlaylistWithNoMediaWeKnowAboutException;
use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdatePlaylistsForChannelCommandTest extends CommandTestCase
{
    use IsFakingYoutube;
    use RefreshDatabase;

    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();

        /* $this->fakePlaylistItemsResponse(
            expectedPlaylistId: $this->playlist->youtube_playlist_id,
        ); */
    }

    /** @test */
    public function channel_id_is_required(): void
    {
        $playlist = Playlist::factory()
            ->for(Channel::factory())
            ->create(['active' => false])
        ;

        $this->expectException(RuntimeException::class);
        $this->artisan('update:playlist');
    }

    /** @test */
    public function no_active_playlist_should_return_1(): void
    {
        $playlist = Playlist::factory()
            ->for(Channel::factory())
            ->create(['active' => false])
        ;

        $this->artisan('update:playlist', ['channel_id' => $playlist->channel->channelId()])->assertExitCode(1);
    }

    /** @test */
    public function active_playlist_with_no_media_should_throw_exception(): void
    {
        $playlist = Playlist::factory()
            ->for(Channel::factory())
            ->create(['active' => true])
        ;
        $this->fakePlaylistItemsResponse(expectedPlaylistId: $playlist->youtube_playlist_id);

        $this->expectException(PlaylistWithNoMediaWeKnowAboutException::class);
        $this->artisan('update:playlist', ['channel_id' => $playlist->channel->channelId()]);
    }

    /** @test */
    public function active_playlist_with_medias_should_be_published(): void
    {
        $this->markTestIncomplete('You need to fake playlistItems with created medias');
        $playlist = Playlist::factory()
            ->for(Channel::factory())
            ->create(['active' => true])
        ;

        // create downloaded medias

        // fake PlaylistItemsResponse with medias
        $this->fakePlaylistItemsResponse(expectedPlaylistId: $playlist->youtube_playlist_id);

        $this->artisan('update:playlist', ['channel_id' => $playlist->channel->channelId()])->assertExitCode(0);
    }
}
