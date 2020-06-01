<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubePlaylists;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistsTest extends TestCase
{
    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testChannelIsGettingTheRightUploadsPlaylist()
    {
        $this->assertEquals(
            self::MY_PERSONAL_UPLOADS_PLAYLIST_ID,
            ($playlists = new YoutubePlaylists())
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->uploadsPlaylistId()
        );
        $this->assertEqualsCanonicalizing(
            [$playlists->apikey() => 3],
            YoutubeQuotas::forUrls($playlists->queriesUsed())->quotaConsumed()
        );
    }

    public function testChannelIsGettingTheRightFavoritesPlaylist()
    {
        $this->assertEquals(
            'FLw6bU9JT_Lihb2pbtqAUGQw',
            ($playlists = new YoutubePlaylists())
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->favoritesPlaylistId()
        );
        $this->assertEqualsCanonicalizing(
            [$playlists->apikey() => 3],
            YoutubeQuotas::forUrls($playlists->queriesUsed())->quotaConsumed()
        );
    }

    public function testInvalidChannelIdShouldThrowAnException()
    {
        $this->expectException(YoutubeNoResultsException::class);
        (new YoutubePlaylists())
            ->forChannel('ForSureThisChannelWillNeverEverExist')
            ->uploadsPlaylistId();
    }
}
