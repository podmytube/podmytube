<?php

namespace App\Youtube;

class YoutubeChannelVideos
{
    /** @var \App\Youtube\YoutubeCore $youtubeCore  */
    protected $youtubeCore;
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var string $uploadsPlaylistId $youtube 'uploads' playlist id */
    protected $uploadsPlaylistId;
    /** @var array $videoIds pile of video ids */
    protected $videoIds = [];

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
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = YoutubeChannelPlaylists::init(
            $this->youtubeCore
        )
            ->forChannel($this->channelId)
            ->uploadsPlaylistId();

        $this->obtainVideos();

        return $this;
    }

    public function obtainVideos()
    {
        /**
         * get all the uploaded videos for that playlist
         */
        $results = $this->youtubeCore
            ->defineEndpoint('playlistItems.list')
            ->clearParams()
            ->addParams([
                'playlistId' => $this->uploadsPlaylistId,
                'maxResults' => 50,
            ])
            ->addParts(['id', 'snippet'])
            ->url();
        /* run()
         ->results(); */
        dump($results);
    }

    public function videos()
    {
        return $this->videoIds;
    }
}
