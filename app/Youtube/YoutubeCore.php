<?php

namespace App\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeQueryFailureException;
use App\Traits\YoutubeEndpoints;

class YoutubeCore
{
    use YoutubeEndpoints;

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
    /** @var array $partParams youtube part parameters */
    protected $partParams = [];

    private function __construct(string $apikey)
    {
        $this->params['key'] = $apikey;
        $this->params['part'] = [];
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function defineEndpoint(string $endpoint)
    {
        if (!isset($this->endpointUrlMap[$endpoint])) {
            throw new YoutubeInvalidEndpointException(
                "Specified endpoint {$endpoint} does not exists."
            );
        }
        $this->endpoint = $endpoint;
        return $this;
    }

    public function endpoint()
    {
        return $this->endpoint;
    }

    public function url()
    {
        return $this->endpointUrlMap[$this->endpoint] .
            '?' .
            http_build_query($this->params());
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
        dump($this->url()); 
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
        if (isset($this->jsonResult['error'])) {
            $this->errorCode = $this->jsonResult['error']['code'];
            $this->errorMessage = $this->jsonResult['error']['message'];
            return true;
        }
        return false;
    }

    /**
     * Add some part params to the query.
     * Because part params are different for every endpoint,
     * this one should be set before.
     *
     * @param array $parts
     */
    public function addParts(array $parts, $delim = ',')
    {
        if (!isset($this->endpoint)) {
            throw new YoutubeInvalidEndpointException(
                'Endpoint not defined. You should set one before.'
            );
        }
        $validEndpointsParts = array_keys(
            $this->endpointPartMap[$this->endpoint]
        );
        $this->partParams = array_unique(
            array_merge(
                $this->partParams,
                array_filter($parts, function ($partToCheck) use (
                    $validEndpointsParts
                ) {
                    return in_array($partToCheck, $validEndpointsParts);
                })
            )
        );
        return $this;
    }

    public function addParams(array $attributes = [])
    {
        $this->params = array_merge($this->params, $attributes);
        return $this;
    }

    public function params()
    {
        $this->params['part'] = implode(',', $this->partParams());
        ksort($this->params);
        return $this->params;
    }

    public function results()
    {
        return $this->jsonResult;
    }

    public function partParams()
    {
        return $this->partParams;
    }
}
