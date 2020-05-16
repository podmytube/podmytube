<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeChannelVideos;

class YoutubeChannelVideosTest extends YoutubeCoreTest
{
    public function testUploadsPlaylistId()
    {
        $this->assertEquals(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelVideos::init($this->apikey)
                ->channel(self::PERSONAL_CHANNEL_ID)
                ->uploadPlaylistId()
        );
    }

    public function testGettingUploadsPlaylistIsFine()
    {
        $this->assertCount(
            2,
            YoutubeChannelVideos::init($this->apikey)
                ->channel(self::PERSONAL_CHANNEL_ID)
                ->videos()
        );
    }
}
