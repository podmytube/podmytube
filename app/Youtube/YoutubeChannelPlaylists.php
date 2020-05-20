<?php

namespace App\Youtube;

class YoutubeChannelPlaylists
{
    /** @var \App\Youtube\YoutubeCore $youtubeCore  */
    protected $youtubeCore;
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $playlistIds ['uploads' => 'id1', 'xyz' => 'id2' ]*/
    protected $playlistIds = [];

    private function __construct(YoutubeCore $youtubeCore)
    {
        $this->youtubeCore = $youtubeCore;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function forChannel(string $channelId)
    {
        $this->channelId = $channelId;

        $items = $this->youtubeCore
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
        return $this;
    }

    /**
     * Time to keep the cache.
     *
     */
    protected function cacheDuration()
    {
        return now()->addDays(1);
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
