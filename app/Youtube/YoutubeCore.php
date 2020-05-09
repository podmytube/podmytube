<?php

namespace App\Youtube;

use App\Exceptions\YoutubeQueryFailureException;

class YoutubeCore
{
    /** @var $endpoint endpoint to url mapping */
    protected $endpointUrlMap = [
        'videos.list' => 'https://www.googleapis.com/youtube/v3/videos',
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
        'channels.list' => 'https://www.googleapis.com/youtube/v3/channels',
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists',
        'playlistItems.list' =>
            'https://www.googleapis.com/youtube/v3/playlistItems',
    ];

    protected $endpointPartMap = [
        'channels.list' => [
            'id',
            'snippet',
            'brandingSettings',
            'contentDetails',
            'invideoPromotion',
            'statistics',
            'status',
            'topicDetail',
        ],
        'videos.list' => [
            'id',
            'contentDetails',
            'fileDetails',
            'liveStreamingDetails',
            'localizations',
            'player',
            'processingDetails',
            'recordingDetails',
            'snippet',
            'statistics',
            'status',
            'suggestions',
            'topicDetails',
        ],
        'playlists.list' => [
            'id',
            'player',
            'contentDetails',
            'localizations',
            'snippet',
            'status',
        ],
        'playlistItems.list' => ['id', 'contentDetails', 'snippet', 'status'],
        'search.list' => '',
    ];

    /** @var string $baseUrl */
    protected $baseUrl;
    /** @var string $endpoint */
    protected $endpoint;
    /** @var string $sslpath */
    protected $sslPath;
    /** @var string $referer */
    protected $referer;
    /** @var string $jsonResult */
    protected $jsonResult;
    /** @var int $errorCode */
    protected $errorCode = 0;
    /** @var string $errorMessage */
    protected $errorMessage;
    /** @var array $params query parameters */
    protected $params = [];

    private function __construct(string $apikey)
    {
        $this->params['key'] = $apikey;
        $this->params['part'] = [];
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function endpoint(string $endpoint)
    {
        if (!isset($this->endpointUrlMap[$endpoint])) {
            throw new \InvalidArgumentException(
                "Specified endpoint {$endpoint} does not exists."
            );
        }
        $this->endpoint = $endpoint;
        $this->baseUrl = $this->endpointUrlMap[$endpoint];
        return $this;
    }

    public function url()
    {
        return $this->baseUrl .
            (strpos($this->baseUrl, '?') === false ? '?' : '') .
            http_build_query($this->filteredParams());
    }

    public function run()
    {
        $tuCurl = curl_init();
        if ($this->sslPath !== null) {
            curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($tuCurl, CURLOPT_CAINFO, __DIR__ . '/cert/cacert.pem');
            curl_setopt($tuCurl, CURLOPT_CAPATH, __DIR__ . '/cert/cacert.pem');
        }
        curl_setopt($tuCurl, CURLOPT_URL, $this->url());
        if ($this->referer !== null) {
            curl_setopt($tuCurl, CURLOPT_REFERER, $this->referer);
        }
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        $this->jsonResult = json_decode(curl_exec($tuCurl), true);

        if (curl_errno($tuCurl)) {
            throw new \InvalidArgumentException(
                'Curl Error : ' . curl_error($tuCurl),
                curl_errno($tuCurl)
            );
        }

        if ($this->queryHasFailed()) {
            throw new YoutubeQueryFailureException(
                "Query has failed with message : {$this->errorMessage}",
                $this->errorCode
            );
        }

        return $this;
    }

    public function queryHasFailed()
    {
        if ($this->jsonResult['error'] !== null) {
            $this->errorCode = $this->jsonResult['error']['code'];
            $this->errorMessage = $this->jsonResult['error']['message'];
            return true;
        }
        return false;
    }

    /**
     * @param string|array $parts
     */
    public function addParts($parts, $delim = ',')
    {
        if (is_string($parts)) {
            $parts = explode($delim, $parts);
        }
        $this->params['part'] = array_merge($this->params['part'], $parts);
        return $this;
    }

    /**
     * @param string $part
     */
    public function addPart(string $part)
    {
        $this->params['part'][] = $part;
        return $this;
    }

    public function addParams(array $attributes = [])
    {
        $this->params = array_merge($this->params, $attributes);
        return $this;
    }

    public function params()
    {
        ksort($this->params);
        return $this->params;
    }

    /**
     * Filter part params according to endpoint.
     * This one is called right before the query to remove parts that are not used
     * by the endpoint.
     */
    private function filteredParams()
    {
        $validEndpointsParts = $this->endpointPartMap[$this->endpoint];
        $this->params['part'] = implode(
            ',',
            array_filter(array_unique($this->params['part']), function (
                $partToCheck
            ) use ($validEndpointsParts) {
                return in_array($partToCheck, $validEndpointsParts);
            })
        );
        ksort($this->params);
        return $this->params;
    }
}
