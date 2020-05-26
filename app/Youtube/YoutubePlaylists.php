<?php

namespace App\Youtube;

/**
 * This class intends to get channels's playlist oredered by name.
 * 'uploads' => xliqsjfdumsldodsikpqs
 * 'favorites' => msldodsikpqsxliqsjfdu
 */
class YoutubePlaylists
{
    /** @var \App\Youtube\YoutubeCore $youtubeCore  */
    protected $youtubeCore;
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $playlistIds ['uploads' => 'id1', 'xyz' => 'id2' ]*/
    protected $playlistIds = [];

    private function __construct(string $channelId)
    {
        $this->channelId = $channelId;
        $items = YoutubeCore::init()
            ->defineEndpoint('channels.list')
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
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
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
