<?php

namespace App\Youtube;

class YoutubeChannel extends YoutubeCore
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $results result of youtube query */
    protected $results;

    public function forChannel(
        string $channelId,
        array $parts = ['id', 'snippet']
    ): self {
        $this->channelId = $channelId;
        $this->results = $this->defineEndpoint('/youtube/v3/channels')
            ->addParams(['id' => $channelId])
            ->addParts($parts)
            ->run()
            ->results();
        return $this;
    }

    public function exists()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->channelId === $this->results['items'][0]['id'];
    }

    public function name()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->results['items'][0]['snippet']['title'];
    }
}
