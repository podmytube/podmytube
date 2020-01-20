<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\FeedDoesNotExist;

use Illuminate\Support\Facades\Storage;

class PodcastUrl
{
    public const _FEED_FILENAME = 'podcast.xml';

    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function get()
    {
        return getenv('PODCASTS_URL') . DIRECTORY_SEPARATOR . $this->channel->channelId() . DIRECTORY_SEPARATOR . self::_FEED_FILENAME;
    }
}
