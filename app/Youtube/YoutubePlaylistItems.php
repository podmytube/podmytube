<?php

namespace App\Youtube;

use Carbon\Carbon;

class YoutubePlaylistItems extends YoutubeCore
{
    /** @var string $playlistId $youtube playlist id */
    protected $playlistId;
    /** @var int $cumulatedQuotasUsed */
    protected $cumulatedQuotasUsed = 0;
    /** @var array $videos pile of video obtained from youtube api */
    protected $videos = [];

    public function forPlaylist(string $playlistId): self
    {
        $this->playlistId = $playlistId;
        /**
         * get all the uploaded videos for that playlist
         */
        $videos = $this->defineEndpoint('/youtube/v3/playlistItems')
            ->addParams([
                'playlistId' => $this->playlistId,
                'maxResults' => 50,
            ])
            ->addParts(['id', 'snippet', 'contentDetails'])
            ->run()
            ->items();

        $this->videos = array_map(function ($videoItem) {
            return [
                'videoId' => $videoItem['contentDetails']['videoId'],
                'playlist_id' => $videoItem['snippet']['playlistId'],
                'title' => $videoItem['snippet']['title'],
                'description' => $videoItem['snippet']['description'],
                'published_at' => (new Carbon(
                    $videoItem['contentDetails']['videoPublishedAt']
                ))->setTimezone('UTC'),
            ];
        }, $videos);
        return $this;
    }

    public function videos()
    {
        return $this->videos;
    }
}
