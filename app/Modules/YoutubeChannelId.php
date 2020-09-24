<?php

namespace App\Modules;

use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;

class YoutubeChannelId
{
    /** @var string $channel_id youtube channel id */
    protected $channel_id;

    public static function fromUrl(...$params)
    {
        return new static(...$params);
    }

    private function __construct(string $channelUrl)
    {
        $this->channelUrl = $channelUrl;

        $this->isValidUrl();
        $this->isYoutubeUrl();

        /**
         * checking the url given.
         * It should contain one youtube url the channel path and the channel_id
         */
        if (
            !preg_match(
                "#^/channel/(?'channel'[A-Za-z0-9_-]*)/?$#",
                parse_url($this->channelUrl, PHP_URL_PATH),
                $matches
            )
        ) {
            throw new ChannelCreationInvalidChannelUrlException(
                'This channel url is invalid.'
            );
        }

        $this->channel_id = $matches['channel'];
    }

    public function get()
    {
        return $this->channel_id;
    }

    protected function isValidUrl()
    {
        /**
         * url should be one
         */
        if (
            !filter_var(
                $this->channelUrl,
                FILTER_VALIDATE_URL,
                FILTER_FLAG_PATH_REQUIRED
            )
        ) {
            throw new ChannelCreationInvalidUrlException(
                'flash_channel_id_is_invalid'
            );
        }
        return true;
    }

    protected function isYoutubeUrl()
    {
        if (
            !in_array(parse_url($this->channelUrl, PHP_URL_HOST), [
                'youtube.com',
                'www.youtube.com',
            ])
        ) {
            throw new ChannelCreationOnlyYoutubeIsAccepted(
                'Only channels from youtube are accepted !'
            );
        }
        return true;
    }
}
