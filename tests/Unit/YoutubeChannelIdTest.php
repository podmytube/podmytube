<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Modules\YoutubeChannelId;

class YoutubeChannelIdTest extends TestCase
{
    public function testingInvalidCharactersInChannelId()
    {
        $this->expectException(
            ChannelCreationInvalidChannelUrlException::class
        );
        YoutubeChannelId::fromUrl(
            'http://www.youtube.com/channel/UCw%*Lihb2pbtqAUGQw-/'
        );
    }

    public function testingInvalidHostInChannelId()
    {
        $this->expectException(ChannelCreationOnlyYoutubeIsAccepted::class);
        YoutubeChannelId::fromUrl(
            'http://www.vimeo.com/channel/UCw%*Lihb2pbtqAUGQw-/'
        );
    }

    public function testingInvalidYoutubeUrl()
    {
        $this->expectException(ChannelCreationInvalidUrlException::class);
        YoutubeChannelId::fromUrl('This is not one url');
    }

    public function testingValidYoutubeUrls()
    {
        $expectedChannelId = self::PERSONAL_CHANNEL_ID;
        foreach ([
            'http://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '/',
            'http://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '',
            'https://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '/',
            'https://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '',
            'https://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '?',
            'https://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '?view_as=subscriber',
            'https://www.youtube.com/channel/' . self::PERSONAL_CHANNEL_ID . '?MyTailorIsRich&view_as=subscriber&whateverYouMightTypeAfter',
        ]
            as $youtubeUrl) {
            $this->assertEquals(
                $expectedChannelId,
                $result = YoutubeChannelId::fromUrl($youtubeUrl)->get(),
                "ChannelId for {$youtubeUrl} should be {$expectedChannelId} and result was {$result}"
            );
        }
    }
}
