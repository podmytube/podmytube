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

    /** @var array $playlistIds ['uploads' => 'id1', 'xyz' => 'id2' ]*/
    protected $playlistIds = [];

    public function forChannel(string $channelId): self
    {
        $this->channelId = $channelId;
        $items = $this->defineEndpoint('/youtube/v3/channels')
            ->addParams([
                'id' => $this->channelId,
            ])
            ->addParts(['id', 'contentDetails'])
            ->run()
            ->items();
        foreach (
            $items[0]['contentDetails']['relatedPlaylists']
            as $playlistName => $playlistId
        ) {
            $this->playlistIds[$playlistName] = $playlistId;
        }
        return $this;
    }

    /**
     * return the 'uploads' playlist id.
     *
     * @return string the uploads playlist id
     */
    public function uploadsPlaylistId()
    {
        return $this->playlistIds['uploads'];
    }

    public function favoritesPlaylistId()
    {
        return $this->playlistIds['favorites'];
    }
}
