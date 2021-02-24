<?php

namespace App\Youtube;

class YoutubeChannel extends YoutubeCore
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $results result of youtube query */
    protected $results;

    public function forChannel(string $channelId, array $parts = ['id', 'snippet']): self
    {
        $this->channelId = $channelId;
        $this->results = $this->defineEndpoint('/youtube/v3/channels')
            ->addParams(['id' => $channelId])
            ->addParts($parts)
            ->run()
            ->results();
        return $this;
    }

    /**
     * check channel id existence on youtube api.
     *
     * @return bool true is channel exists
     *
     * @throws App\Exceptions\YoutubeNoResultsException if channel does not exists
     */
    public function exists() :bool
    {
        return $this->channelId === $this->results['items'][0]['id']; // double check
    }

    public function name() : ?string
    {
        return $this->results['items'][0]['snippet']['title'];
    }

    public function description() :?string
    {
        return $this->results['items'][0]['snippet']['description'];
    }

    public function uploadsPlaylistId() :?string
    {
        return $this->results['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? false;
    }
}
