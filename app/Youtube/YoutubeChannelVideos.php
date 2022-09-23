<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Interfaces\QuotasConsumer;

class YoutubeChannelVideos implements QuotasConsumer
{
    protected string $channelId;
    protected int $limit = 0;
    protected string $uploadsPlaylistId;
    protected array $videos = [];
    protected array $queries = [];
    protected string $apikey = '';

    /**
     * retrieve videos for one specified channel.
     *
     * @param string $channelId channel id wanted
     * @param int    $limit     max number of items wanted (0 = unlimited)
     */
    private function __construct(string $channelId, ?int $limit = 0)
    {
        $this->channelId = $channelId;
        $this->limit = $limit;
        $this->obtainUploadPlaylistIdBeingSmart();
        $this->obtainVideos();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    public function videos(): array
    {
        return $this->videos;
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }

    public function apikey(): ?string
    {
        return $this->apikey;
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

    protected function obtainVideos(): void
    {
        // get all the uploaded videos for that playlist
        $this->videos = ($playlistItems = new YoutubePlaylistItems())
            ->setLimit($this->limit)
            ->forPlaylist($this->uploadsPlaylistId)
            ->videos()
        ;
        $this->queries = array_merge($this->queries, $playlistItems->queriesUsed());
    }
}
