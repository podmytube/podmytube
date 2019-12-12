<?php

namespace Tests\Unit;

use App\Channel;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;
use Tests\TestCase;

class ChannelModelTest extends TestCase
{
    public function testingInvalidCharactersInChannelId()
    {
        $this->expectException(ChannelCreationInvalidChannelUrlException::class);
        $result = Channel::extractChannelIdFromUrl("http://www.youtube.com/channel/UCw%*Lihb2pbtqAUGQw-/");
    }

    public function testingInvalidHostInChannelId()
    {
        $this->expectException(ChannelCreationOnlyYoutubeIsAccepted::class);
        $result = Channel::extractChannelIdFromUrl("http://www.vimeo.com/channel/UCw%*Lihb2pbtqAUGQw-/");
    }

    public function testingInvalidYoutubeUrl()
    {
        $this->expectException(ChannelCreationInvalidUrlException::class);
        Channel::extractChannelIdFromUrl("This is not one url");
    }

    public function testingValidYoutubeUrls()
    {
        $expectedChannelId = 'UCw6bU9JT_Lihb2pbtqAUGQw-';
        foreach ([
            "http://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-/",
            "http://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-/",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-?",
            "https://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-?view_as=subscriber",
        ] as $youtubeUrl) {
            $this->assertEquals(
                $expectedChannelId,
                $result = Channel::extractChannelIdFromUrl($youtubeUrl),
                "ChannelId for {$youtubeUrl} should be {$expectedChannelId} and result was {$result}"
            );
        }
    }
}
