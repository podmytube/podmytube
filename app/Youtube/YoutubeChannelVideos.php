<?php

namespace App\Youtube;

use App\Interfaces\QuotasConsumer;

class YoutubeChannelVideos implements QuotasConsumer
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var string $uploadsPlaylistId $youtube 'uploads' playlist id */
    protected $uploadsPlaylistId;
    /** @var array $videos pile of video obtained from youtube api */
    protected $videos = [];
    /** @var array $queries */
    protected $queries = [];
    /** @var string $apikey */
    protected $apikey;

    public function __construct()
    {
    }

    public function forChannel(string $channelId): self
    {
        $this->channelId = $channelId;
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = ($playlist = new YoutubePlaylists())
            ->forChannel($this->channelId)
            ->uploadsPlaylistId();

        $this->apikey = $playlist->apikey();
        $this->queries = array_merge($this->queries, $playlist->queriesUsed());
        $this->obtainVideos();
        return $this;
    }

    protected function obtainVideos()
    {
        /**
         * get all the uploaded videos for that playlist
         */
        $this->videos = ($playlistItems = new YoutubePlaylistItems())
            ->forPlaylist($this->uploadsPlaylistId)
            ->videos();
        $this->queries = array_merge(
            $this->queries,
            $playlistItems->queriesUsed()
        );
    }

    public function videos()
    {
        return $this->videos;
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }

    public function apikey()
    {
        return $this->apikey;
    }
}
