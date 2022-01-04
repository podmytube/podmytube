<?php

declare(strict_types=1);

namespace App\Youtube;

class YoutubeChannel extends YoutubeCore
{
    /** @var string $youtube channel id */
    protected $channelId;

    /** @var array result of youtube query */
    protected $results;

    public function forChannel(string $channelId, array $parts = ['id', 'snippet']): self
    {
        $this->channelId = $channelId;
        $this->results = $this->defineEndpoint('/youtube/v3/channels')
            ->addParams(['id' => $channelId])
            ->addParts($parts)
            ->run()
            ->results()
        ;

        return $this;
    }

    public function name(): ?string
    {
        return $this->results['items'][0]['snippet']['title'];
    }

    public function description(): ?string
    {
        return $this->results['items'][0]['snippet']['description'];
    }

    public function uploadsPlaylistId(): ?string
    {
        return $this->results['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? false;
    }
}
