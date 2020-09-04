<?php

namespace App\Youtube;

use App\Interfaces\QuotasConsumer;

class YoutubeChannelVideos implements QuotasConsumer
{
    /** @var string $channelId youtube channel id */
    protected $channelId;
    /** @var int $limit number of items wanted (0=unlimited) */
    protected $limit = 0;
    /** @var string $uploadsPlaylistId $youtube 'uploads' playlist id */
    protected $uploadsPlaylistId;
    /** @var array $videos pile of video obtained from youtube api */
    protected $videos = [];
    /** @var array $queries */
    protected $queries = [];
    /** @var string $apikey */
    protected $apikey;

    /**
     * retrieve videos for one specified channel.
     *
     * @param string $channelId channel id wanted
     * @param int $limit max number of items wanted (0 = unlimited)
     */
    public function forChannel(string $channelId, $limit = 0): self
    {
        $this->channelId = $channelId;
        $this->limit = $limit;
        $this->obtainUploadPlaylistIdBeingSmart();
        //$this->obtainUploadsPlaylistIdFromYoutube();
        $this->obtainVideos();
        return $this;
    }

    /**
     * obtain 'uploads' playlist id the quickest/cheapest way.
     * uploads playlist id is the channel id where second letter has been replaced by 'U'.
     * UCxxxxxxxxxxx => UUxxxxxxxxxxx.
     * It's a trick to accelerate channel update process and reduce quota usage but I'm not sure
     * it will last forever.
     */
    protected function obtainUploadPlaylistIdBeingSmart(): void
    {
        $this->uploadsPlaylistId = $this->channelId;
        $this->uploadsPlaylistId[1] = 'U';
    }

    /**
     * getting 'uploads' playlist id the normal way.
     */
    protected function obtainUploadsPlaylistIdFromYoutube(): void
    {
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = ($playlist = new YoutubePlaylists())
            ->forChannel($this->channelId)
            ->uploadsPlaylistId();

        $this->apikey = $playlist->apikey();
        $this->queries = array_merge($this->queries, $playlist->queriesUsed());
    }

    protected function obtainVideos(): void
    {
        /**
         * get all the uploaded videos for that playlist
         */
        $this->videos = ($playlistItems = new YoutubePlaylistItems())
            ->setLimit($this->limit)
            ->forPlaylist($this->uploadsPlaylistId)
            ->videos();
        $this->queries = array_merge(
            $this->queries,
            $playlistItems->queriesUsed()
        );
    }

    public function videos(): array
    {
        return $this->videos;
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }

    public function apikey(): string
    {
        return $this->apikey;
    }
}
