<?php

namespace App\Youtube;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class YoutubePlaylistItems extends YoutubeCore
{
    /** @var string $playlistId $youtube playlist id */
    protected $playlistId;

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

        /**
         * filtering invalid publishedAt video
         */
        $this->videos = array_filter($videos, function ($item) {
            if (!isset($item['contentDetails']['videoPublishedAt']) || !strlen($item['contentDetails']['videoPublishedAt'])) {
                return false;
            }
            try {
                Carbon::parse($item['contentDetails']['videoPublishedAt']);
            } catch (\Exception $exception) {
                Log::error("Media id : {$item['contentDetails']['videoId']} publication date {$item['contentDetails']['videoPublishedAt']} is invalid ");
                return false;
            }
            return true;
        });

        $this->videos = array_map(function ($videoItem) {
            return [
                'media_id' => $videoItem['contentDetails']['videoId'],
                'playlist_id' => $videoItem['snippet']['playlistId'],
                'title' => $videoItem['snippet']['title'],
                'description' => $videoItem['snippet']['description'],
                'published_at' => Carbon::parse($videoItem['contentDetails']['videoPublishedAt'])->setTimezone('UTC'),
            ];
        }, $videos);
        return $this;
    }

    public function videos()
    {
        return $this->videos;
    }
}
