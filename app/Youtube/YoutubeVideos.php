<?php

namespace App\Youtube;

use Carbon\Carbon;

class YoutubeVideos extends YoutubeCore
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var string $uploadsPlaylistId $youtube 'uploads' playlist id */
    protected $uploadsPlaylistId;
    /** @var int $cumulatedQuotasUsed */
    protected $cumulatedQuotasUsed = 0;
    /** @var array $videos pile of video obtained from youtube api */
    protected $videos = [];

    public function forChannel(string $channelId): self
    {
        $this->channelId = $channelId;
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = YoutubePlaylists::init()
            ->forChannel($this->channelId)
            ->uploadsPlaylistId();
        $this->cumulatedQuotasUsed += $this->quotasUsed();
        $this->obtainVideos();
        return $this;
    }

    protected function obtainVideos()
    {
        /**
         * get all the uploaded videos for that playlist
         */
        $videos = $this->defineEndpoint('playlistItems.list')
            ->addParams([
                'playlistId' => $this->uploadsPlaylistId,
                'maxResults' => 50,
            ])
            ->addParts(['id', 'snippet', 'contentDetails'])
            ->run()
            ->items();
        $this->cumulatedQuotasUsed += $this->quotasUsed();

        $this->videos = array_map(function ($videoItem) {
            return [
                'videoId' => $videoItem['contentDetails']['videoId'],
                'channel_id' => $videoItem['snippet']['channelId'],
                'title' => $videoItem['snippet']['title'],
                'description' => $videoItem['snippet']['description'],
                'published_at' => (new Carbon(
                    $videoItem['contentDetails']['videoPublishedAt']
                ))->setTimezone('UTC'),
            ];
        }, $videos);
    }

    public function videos()
    {
        return $this->videos;
    }
}
