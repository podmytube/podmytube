<?php

namespace App\Traits;

trait YoutubeEndpoints
{
    protected $endpointMap = [
        '/youtube/v3/channels' => [
            'base' => 1,
            'part' => [
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
        ],
        '/youtube/v3/playlistItems' => [
            'base' => 1,
            'part' => [
                'contentDetails' => 2,
                'id' => 0,
                'snippet' => 2,
                'status' => 2,
            ],
        ],
        '/youtube/v3/playlists' => [
            'base' => 1,
            'part' => [
                'contentDetails' => 2,
                'id' => 0,
                'localizations' => 2,
                'player' => 0,
                'snippet' => 2,
                'status' => 2,
            ],
        ],
        '/youtube/v3/search' => [
            'base' => 100,
            'part' => [],
        ],
        '/youtube/v3/videos' => [
            'base' => 1,
            'part' => [
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
        ],
    ];

    public function getEndpointMap(string $endpoint)
    {
        return $this->endpointPartMap[$endpoint];
    }

    public function getEndpointPartMap(string $endpoint)
    {
        return $this->endpointPartMap[$endpoint];
    }

    /**
     * check the endpoint existence.
     * Throw exception if uknown.
     *
     * @param string $endpoint
     *
     * @return bool
     *
     * @throws InvalidArgumentException if not found
     */
    public function checkEndpoint(string $endpoint): bool
    {
        if (!isset($this->endpointMap[$endpoint])) {
            throw new \InvalidArgumentException(
                "Endpoint {$endpoint} is unknown.}"
            );
        }
        return true;
    }

    protected function endpointParts($endpoint)
    {
        $this->checkEndpoint($endpoint);
        return array_keys($this->endpointMap[$endpoint]['part']);
    }

    protected function baseQuotaCost(string $endpoint): int
    {
        $this->checkEndpoint($endpoint);
        return $this->endpointMap[$endpoint]['base'];
    }

    protected function partQuotaCost($endpoint, $part)
    {
        $this->checkEndpoint($endpoint);
        if (!isset($this->endpointMap[$endpoint]['part'][$part])) {
            return 0;
        }
        return $this->endpointMap[$endpoint]['part'][$part];
    }
}
