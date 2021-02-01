<?php

namespace App\Youtube;

/**
 * This class intends to get channels's playlist oredered by name.
 * 'uploads' => xliqsjfdumsldodsikpqs
 * 'favorites' => msldodsikpqsxliqsjfdu
 */
class YoutubePlaylists extends YoutubeCore
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;

    /** @var array $playlists */
    protected $playlists = [];

    public function forChannel(string $channelId): self
    {
        $this->channelId = $channelId;
        $playlistItems = $this->defineEndpoint('/youtube/v3/playlists')
            ->addParams([
                'channelId' => $this->channelId,
            ])
            ->addParts(['id', 'contentDetails', 'snippet'])
            ->run()
            ->items();

        foreach ($playlistItems as $playlistItem) {
            $this->playlists[$playlistItem['id']] = [
                'id' => $playlistItem['id'],
                'title' => $playlistItem['snippet']['title'],
                'description' => $playlistItem['snippet']['description'],
                'nbVideos' => $playlistItem['contentDetails']['itemCount'],
            ];
        }
        return $this;
    }

    public function playlists()
    {
        return $this->playlists;
    }
}
