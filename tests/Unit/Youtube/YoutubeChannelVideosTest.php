<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeChannelVideos;

class YoutubeChannelVideosTest extends YoutubeCoreTest
{
    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            YoutubeChannelVideos::init($this->youtubeCore)
                ->forChannel(self::PERSONAL_CHANNEL_ID)
                ->videos()
        );
    }
}
