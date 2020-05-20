<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeChannelPlaylists;
use Illuminate\Support\Facades\Config;

class YoutubeChannelPlayListsTest extends YoutubeCoreTest
{
    public function testChannelIsGettingTheRightUploadsPlaylist()
    {
        $this->assertEquals(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelPlaylists::init($this->youtubeCore)
                ->forChannel(self::PERSONAL_CHANNEL_ID)
                ->uploadsPlaylistId()
        );
    }

    public function testChannelIsGettingTheRightFavoritesPlaylist()
    {
        $this->assertEquals(
            'FLw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelPlaylists::init($this->youtubeCore)
                ->forChannel(self::PERSONAL_CHANNEL_ID)
                ->favoritesPlaylistId()
        );
    }
}
