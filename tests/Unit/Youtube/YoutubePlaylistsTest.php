<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubePlaylists;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);        
    }

    public function testChannelIsGettingTheRightUploadsPlaylist()
    {
        $this->assertEquals(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            YoutubePlaylists::forChannel(
                YoutubeCoreTest::PERSONAL_CHANNEL_ID
            )->uploadsPlaylistId()
        );
    }

    public function testChannelIsGettingTheRightFavoritesPlaylist()
    {
        $this->assertEquals(
            'FLw6bU9JT_Lihb2pbtqAUGQw',
            YoutubePlaylists::forChannel(
                YoutubeCoreTest::PERSONAL_CHANNEL_ID
            )->favoritesPlaylistId()
        );
    }
}
