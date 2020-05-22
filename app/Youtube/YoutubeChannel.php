<?php

namespace App\Youtube;

use Carbon\Carbon;

class YoutubeChannel
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $results result of youtube query */
    protected $results;

    private function __construct(string $channelId)
    {
        $this->channelId = $channelId;
        $this->results = YoutubeCore::init()
            ->defineEndpoint('channels.list')
            ->addParts(['id', 'snippet'])
            ->addParams(['id' => $channelId])
            ->run()
            ->results();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    protected function hasResult()
    {
        return $this->results['pageInfo']['totalResults'] <= 0;
    }

    public function exists()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->channelId === $this->results['items'][0]['id'];
    }

    public function name()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->results['items'][0]['snippet']['title'];
    }

    public function videos()
    {
        /**
         * get the uploads playlist id
         */
        $this->uploadsPlaylistId = YoutubePlaylists::forChannel(
            $this->channelId
        )->uploadsPlaylistId();

        /**
         * get videos from youtube
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

        /**
         * format results
         */
        $this->videos = array_map(function ($videoItem) {
            return [
                'media_id' => $videoItem['contentDetails']['videoId'],
                'channel_id' => $videoItem['snippet']['channelId'],
                'title' => $videoItem['snippet']['title'],
                'description' => $videoItem['snippet']['description'],
                'published_at' => (new Carbon(
                    $videoItem['contentDetails']['videoPublishedAt']
                ))->setTimezone('UTC'),
            ];
        }, $videos);

        usort($this->videos, function ($item1, $item2) {
            return $item2['published_at'] <=> $item1['published_at'];
        });

        return $this->videos;
    }
}
