<?php

namespace App\Traits;

trait YoutubeEndpoints
{
    /** @var array $endpendpointUrlMapoint endpoint to url mapping */
    protected $endpointUrlMap = [
        'channels.list' => 'https://www.googleapis.com/youtube/v3/channels',
        'playlistItems.list' =>
            'https://www.googleapis.com/youtube/v3/playlistItems',
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists',
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
        'videos.list' => 'https://www.googleapis.com/youtube/v3/videos',
    ];

    protected $endpointPartMap = [
        'channels.list' => [
            'auditDetails' => 4,
            'brandingSettings' => 2,
            'contentDetails' => 2,
            'contentOwnerDetails' => 2,
            'id' => 0,
            'localizations' => 2,
            'snippet' => 2,
            'statistics' => 2,
            'status' => 2,
            'topicDetails' => 2,
        ],
        'playlistItems.list' => [
            'contentDetails' => 2,
            'id' => 0,
            'snippet' => 2,
            'status' => 2,
        ],
        'playlists.list' => [
            'contentDetails' => 2,
            'id' => 0,
            'localizations' => 2,
            'player' => 0,
            'snippet' => 2,
            'status' => 2,
        ],
        'search.list' => '',
        'videos.list' => [
            'contentDetails' => 2,
            'fileDetails' => 1,
            'id' => 0,
            'liveStreamingDetails' => 2,
            'localizations' => 2,
            'player' => 0,
            'processingDetails' => 1,
            'recordingDetails' => 2,
            'snippet' => 2,
            'statistics' => 2,
            'status' => 2,
            'suggestions' => 1,
            'topicDetails' => 2,
        ],
    ];

    public function getEndpointMap(string $endpoint)
    {
        return $this->endpointPartMap[$endpoint];
    }
}
