<?php

namespace App\Youtube;

use App\Exceptions\YoutubeNoResultsException;

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
    public function exists()
    {
        if (!$this->hasResult()) {
            throw new YoutubeNoResultsException(
                "Cannot get information for this channel {$this->channelId}"
            );
        }
        return $this->channelId === $this->results['items'][0]['id']; // double check
    }

    public function name()
    {
        if (!$this->exists()) {
            throw new YoutubeNoResultsException(
                "Cannot get information for this channel {$this->channelId}"
            );
        }
        return $this->results['items'][0]['snippet']['title'];
    }

    public function uploadsPlaylistId() : ?string
    {
        return $this->results['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? false;
    }
}
