<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Playlist;
use App\Thumb;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Traits\IsAbleToTestPodcast;

class PlaylistModelTest extends TestCase
{
    use RefreshDatabase, IsAbleToTestPodcast;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->playlist = factory(Playlist::class)->create();
    }

    public function testPodcastUrlIsFine()
    {
        $this->assertEquals(
            config('app.playlists_url') . '/' . $this->playlist->channel->channel_id . '/' . $this->playlist->youtube_playlist_id . '.xml',
            $this->playlist->podcastUrl()
        );
    }

    public function testMediasToPublishShouldBeFine()
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
        $mediasToPublish->map(function ($media) {
            $this->assertInstanceOf(Media::class, $media);
        });
    }

    public function testPodcastCoverUrlIsFine()
    {
        /** channel has default thumb, so has playlist */
        $this->assertEquals(Thumb::defaultUrl(), $this->playlist->podcastCoverUrl());

        /** channel set a thumb, will be the same for playlist (for the moment) */
        $channelWithThumb = factory(Channel::class)->create();
        $thumb = factory(Thumb::class)->create(['channel_id' => $channelWithThumb->channel_id]);
        $playlistWithThumb = factory(Playlist::class)->create(['channel_id' => $channelWithThumb->channel_id]);
        $this->assertEquals(
            config('app.thumbs_url') . '/' . $thumb->relativePath,
            $playlistWithThumb->podcastCoverUrl()
        );
    }

    public function testingToPodcastHeaderIsFineWithAllInformations()
    {
        $this->podcastHeaderInfosChecking($this->playlist, $this->playlist->podcastHeader());
    }

    public function testingToPodcastHeaderIsFineWithoutSome()
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

    public function testPlaylistToPodcastIsRunningFine()
    {
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $expectedItems = 2;
        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);

        $this->playlist = factory(Playlist::class)->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);
        $playlistToPodcastInfos = $this->playlist->toPodcast();
        /** checking header */
        $this->podcastHeaderInfosChecking($this->playlist, $playlistToPodcastInfos);
        /** checking items */
        $this->assertCount($expectedItems, $playlistToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($playlistToPodcastInfos['podcastItems']);
    }

    public function testRelativeFeedPathIsGood()
    {
        $this->assertEquals(
            $this->playlist->channel->channelId() . '/' . $this->playlist->youtube_playlist_id . '.xml',
            $this->playlist->relativeFeedPath()
        );
    }

    public function testRemoteFilePathIsGood()
    {
        $this->assertEquals(
            config('app.playlists_path') . $this->playlist->channel->channelId() . '/' . $this->playlist->youtube_playlist_id . '.xml',
            $this->playlist->remoteFilePath()
        );
    }

    /** @test */
    public function scope_active_is_ok()
    {
        factory(Playlist::class)->create(['active' => false]);
        $this->playlist->update(['active' => true]);

        /** getting all active playlist (should be only one) */
        $activePlaylists = Playlist::active()->get();
        $this->assertCount(1, $activePlaylists);

        /** filtering on the one I set as active */
        $activePlaylists->filter(function ($activePlaylist) {
            return $activePlaylist->id === $this->playlist->id;
        });

        /** if filtered playlist has only 1 item and the right one it's good */
        $this->assertCount(1, $activePlaylists);
        $this->assertEquals($this->playlist->id, $activePlaylists->first()->id);
    }

    /** @test */
    public function user_has_no_playlists_should_return_zero()
    {
        $expectedNumberOfPlaylists = 0;
        $user = factory(User::class)->create();
        /** user has no playlist yet */
        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($user));
    }

    /** @test */
    public function user_playlists_should_be_fine()
    {
        /** associating one new channel to this user */
        $channel = $this->createChannelForUser($this->user);
        $this->playlist->update(['channel_id' => $channel->channelId()]);

        /** user should have one playlist now */
        $expectedNumberOfPlaylists = 1;
        $this->assertCount(1, Playlist::userPlaylists($this->user));

        /** creating some playlists on same channel */
        $numberOfPlaylistsToAdd = 5;
        factory(Playlist::class, $numberOfPlaylistsToAdd)->create(['channel_id' => $channel->channelId()]);
        $expectedNumberOfPlaylists += $numberOfPlaylistsToAdd;

        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($this->user));

        /** associating another channel with some playlists */
        $anotherChannel = $this->createChannelForUser($this->user);
        $numberOfPlaylistsToAdd = 3;
        factory(Playlist::class, $numberOfPlaylistsToAdd)->create(['channel_id' => $anotherChannel->channelId()]);

        $expectedNumberOfPlaylists += $numberOfPlaylistsToAdd;
        $this->assertCount($expectedNumberOfPlaylists, Playlist::userPlaylists($this->user));
    }
}
