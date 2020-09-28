<?php

namespace App\Factories;

use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeVideo;

class YoutubeLastVideoFactory
{
    /** @var string $channel_id */
    protected $channel_id;

    /** @var array $lastMedia */
    protected $lastMedia = [];

    private function __construct(string $channel_id)
    {
        $this->channel_id = $channel_id;
        $this->obtainLastMedia();
        $this->obtainTagsForMedia();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    protected function obtainLastMedia()
    {
        $this->lastMedia = YoutubeChannelVideos::forChannel($this->channel_id, 1)->videos()[0];
    }

    protected function obtainTagsForMedia()
    {
        $this->lastMedia['tags'] = YoutubeVideo::forMedia(
            $this->lastMedia['media_id'],
            ['id', 'status']
        )->tags();
    }

    public function quotasConsumed()
    {
        
    }

    public function lastMedia()
    {
        return $this->lastMedia;
    }
}
