<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeChannelPlaylists;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelPlayListsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->youtubeCore = YoutubeCore::init($this->apikey);
    }

    public function testChannelIsGettingTheRightUploadsPlaylist()
    {
        $this->assertEquals(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelPlaylists::init($this->youtubeCore)
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->uploadsPlaylistId()
        );
    }

    public function testChannelIsGettingTheRightFavoritesPlaylist()
    {
        $this->assertEquals(
            'FLw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelPlaylists::init($this->youtubeCore)
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->favoritesPlaylistId()
        );
    }
}
