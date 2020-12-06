<?php

namespace App\Podcast;

use App\Channel;

class PodcastUrl
{
    public const FEED_FILENAME = 'podcast.xml';

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
        return config('app.podcasts_url') . '/' . $this->channel->id() . '/' . self::FEED_FILENAME;
    }
}
