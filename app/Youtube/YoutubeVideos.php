<?php

namespace App\Youtube;

use Carbon\Carbon;

class YoutubeVideos
{
    /** @var \App\Youtube\YoutubeCore $youtubeCore  */
    protected $youtubeCore;
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var string $uploadsPlaylistId $youtube 'uploads' playlist id */
    protected $uploadsPlaylistId;
    /** @var array $videos pile of video obtained from youtube api */
    protected $videos = [];

    private function __construct(string $channelId)
    {
        $this->channelId = $channelId;
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = YoutubePlaylists::forChannel(
            $this->channelId
        )->uploadsPlaylistId();

        $this->obtainVideos();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    protected function obtainVideos()
    {
        /**
         * get all the uploaded videos for that playlist
         */
        $videos = YoutubeCore::init()
            ->defineEndpoint('playlistItems.list')
            ->clearParams()
            ->addParams([
                'playlistId' => $this->uploadsPlaylistId,
                'maxResults' => 50,
            ])
            ->addParts(['id', 'snippet', 'contentDetails'])
            ->run()
            ->items();

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
