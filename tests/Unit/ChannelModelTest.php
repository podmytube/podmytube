<?php

namespace Tests\Unit;

use App\Channel;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use Tests\TestCase;

class ChannelModelTest extends TestCase
{
    public function testingChannelCreationFailure()
    {
        $this->expectException(ChannelCreationInvalidChannelUrlException::class);
        Channel::extractChannelIdFromUrl("http://www.youtube.com/channel/UCw*6bU9JT_Lihb2pbtqAUGQw-/");
    }

    public function testingInvalidChannelIdFromYoutubeUrl()
    {
        $this->expectException(ChannelCreationInvalidChannelUrlException::class);
        Channel::extractChannelIdFromUrl("http://www.youtube.com/channel/UCw*6bU9JT_Lihb2pbtqAUGQw-/");
    }

    public function testingInvalidYoutubeUrl()
    {
        $this->expectException(ChannelCreationInvalidUrlException::class);
        Channel::extractChannelIdFromUrl("This is not one url");
    }

    public function testingValidYoutubeUrl()
    {
        $expectedChannelId = 'UCw6bU9JT_Lihb2pbtqAUGQw-';
        foreach ([
            "http://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-/",
            "http://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-/",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-",
        ] as $youtubeUrl) {
            $this->assertEquals(
                $expectedChannelId,
                ($result = Channel::extractChannelIdFromUrl($youtubeUrl)),
                "ChannelId for {$youtubeUrl} should be {$expectedChannelId} and result was {$result}"
            );
        }
    }
}
