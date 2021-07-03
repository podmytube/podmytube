<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Media;
use App\Playlist;
use App\Thumb;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tests\Traits\IsAbleToTestPodcast;

/**
 * @internal
 * @covers \App\Playlist
 */
class PlaylistModelTest extends TestCase
{
    use RefreshDatabase;
    use IsAbleToTestPodcast;

    /** @var \App\Playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->channel = $this->createChannel($this->user);
        $this->playlist = factory(Playlist::class)->create(['channel_id' => $this->channel->channelId()]);
    }

    /** @test */
    public function podcast_url_is_fine(): void
    {
        $this->assertEquals(
            config('app.playlists_url').'/'.$this->playlist->channel->channel_id.'/'.$this->playlist->youtube_playlist_id.'.xml',
            $this->playlist->podcastUrl()
        );
    }

    /** @test */
    public function medias_to_publish_should_be_fine(): void
    {
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $expectedMediasToPublish = 2;

        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);

        $this->playlist = factory(Playlist::class)->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);

        $mediasToPublish = $this->playlist->mediasToPublish();
        $this->assertCount($expectedMediasToPublish, $mediasToPublish);
        $this->assertInstanceOf(Collection::class, $mediasToPublish);
        $mediasToPublish->map(function ($media): void {
            $this->assertInstanceOf(Media::class, $media);
        });
    }

    /** @test */
    public function podcast_cover_url_is_fine(): void
    {
        // channel has default thumb, so has playlist
        $this->assertEquals(Thumb::defaultUrl(), $this->playlist->podcastCoverUrl());

        $thumb = factory(Thumb::class)->create(
            [
                'coverable_type' => get_class($this->playlist),
                'coverable_id' => $this->playlist->id(),
            ]
        );
        $this->playlist->refresh();

        $this->assertEquals(
            config('app.thumbs_url')."/{$this->playlist->channelId()}/".$thumb->file_name,
            $this->playlist->podcastCoverUrl()
        );
    }

    /** @test */
    public function to_podcast_header_is_fine_with_all_informations(): void
    {
        $this->podcastHeaderInfosChecking($this->playlist, $this->playlist->podcastHeader());
    }

    /** @test */
    public function to_podcast_header_is_fine_without_some(): void
    {
        $this->playlist->channel->update([
            'podcast_title' => null,
            'podcast_copyright' => null,
            'authors' => null,
            'email' => null,
            'description' => null,
            'link' => null,
            'category_id' => null,
            'language_id' => null,
            'explicit' => false,
        ]);
        $this->podcastHeaderInfosChecking($this->playlist, $this->playlist->podcastHeader());
    }

    /** @test */
    public function playlist_to_podcast_is_running_fine(): void
    {
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $expectedItems = 2;
        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);

        $this->playlist = factory(Playlist::class)->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);
        $playlistToPodcastInfos = $this->playlist->toPodcast();
        // checking header
        $this->podcastHeaderInfosChecking($this->playlist, $playlistToPodcastInfos);
        // checking items
        $this->assertCount($expectedItems, $playlistToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($playlistToPodcastInfos['podcastItems']);
    }

    /** @test */
    public function relative_feed_path_is_good(): void
    {
        $this->assertEquals(
            $this->playlist->channel->channelId().'/'.$this->playlist->youtube_playlist_id.'.xml',
            $this->playlist->relativeFeedPath()
        );
    }

    /** @test */
    public function remote_file_path_is_good(): void
    {
        $this->assertEquals(
            config('app.playlists_path').$this->playlist->channel->channelId().'/'.$this->playlist->youtube_playlist_id.'.xml',
            $this->playlist->remoteFilePath()
        );
    }

    /** @test */
    public function scope_active_is_ok(): void
    {
        factory(Playlist::class)->create(['active' => false]);
        $this->playlist->update(['active' => true]);

        /** getting all active playlist (should be only one) */
        $activePlaylists = Playlist::active()->get();
        $this->assertCount(1, $activePlaylists);

        // filtering on the one I set as active
        $activePlaylists->filter(function ($activePlaylist) {
            return $activePlaylist->id === $this->playlist->id;
        });

        // if filtered playlist has only 1 item and the right one it's good
        $this->assertCount(1, $activePlaylists);
        $this->assertEquals($this->playlist->id, $activePlaylists->first()->id);
    }

    /** @test */
    public function user_has_no_playlists_should_return_zero(): void
    {
        $expectedNumberOfPlaylists = 0;
        $user = factory(User::class)->create();
        // user has no playlist yet
        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($user));
    }

    /** @test */
    public function user_playlists_should_be_fine(): void
    {
        $this->playlist->update(['active' => true]);

        /** user should have one playlist now */
        $expectedNumberOfPlaylists = 1;
        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($this->user));

        /** creating some playlists on same channel */
        $numberOfPlaylistsToAdd = 5;
        factory(Playlist::class, $numberOfPlaylistsToAdd)->create(['channel_id' => $this->channel->channelId(), 'active' => true]);
        $expectedNumberOfPlaylists += $numberOfPlaylistsToAdd;

        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($this->user));

        /** associating another channel with some playlists */
        $anotherChannel = $this->createChannelForUser($this->user);
        $numberOfPlaylistsToAdd = 3;
        factory(Playlist::class, $numberOfPlaylistsToAdd)->create(['channel_id' => $anotherChannel->channelId(), 'active' => true]);

        $expectedNumberOfPlaylists += $numberOfPlaylistsToAdd;
        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($this->user));
    }

    /** @test */
    public function owner_is_fine(): void
    {
        $owner = $this->playlist->owner();
        $this->assertNotNull($owner);
        $this->assertInstanceOf(Authenticatable::class, $owner);
        $this->assertEquals($this->user->lastname, $owner->lastname);
    }

    /** @test */
    public function associated_medias_is_fine(): void
    {
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);

        $this->playlist->update(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);
        $this->playlist->refresh();
        /** no medias */
        $medias = $this->playlist->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(0, $medias);

        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);

        // with some medias
        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);
        $this->playlist->refresh();

        $medias = $this->playlist->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(2, $medias);
    }
}
