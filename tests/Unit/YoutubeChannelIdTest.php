<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;
use App\Modules\YoutubeChannelId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeChannelIdTest extends TestCase
{
    /** @test */
    public function invalid_characters_in_channel_id(): void
    {
        $this->expectException(
            ChannelCreationInvalidChannelUrlException::class
        );
        YoutubeChannelId::fromUrl(
            'http://www.youtube.com/channel/UCw%*Lihb2pbtqAUGQw-/'
        );
    }

    /** @test */
    public function invalid_host_in_channel_id(): void
    {
        $this->expectException(ChannelCreationOnlyYoutubeIsAccepted::class);
        YoutubeChannelId::fromUrl(
            'http://www.vimeo.com/channel/UCw%*Lihb2pbtqAUGQw-/'
        );
    }

    /** @test */
    public function invalid_youtube_url_should_throw_exception(): void
    {
        $this->expectException(ChannelCreationInvalidUrlException::class);
        YoutubeChannelId::fromUrl('This is not one url');
    }

    /** @test */
    public function valid_youtube_urls(): void
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
