<?php

namespace App\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Modules\Query;
use App\Traits\YoutubeEndpoints;
use Illuminate\Support\Facades\Cache;

class YoutubeCore
{
    use YoutubeEndpoints;

    /** @var string $apikey */
    protected $apikey;
    /** @var string $endpoint */
    protected $endpoint;
    /** @var string $sslpath */
    protected $sslPath;
    /** @var string $referer */
    protected $referer;
    /** @var string $jsonResult */
    protected $jsonResult;
    /** @var array $params query parameters */
    protected $params = [];
    /** @var array $partParams youtube part parameters */
    protected $partParams = [];

    private function __construct(string $apikey)
    {
        $this->apikey = $apikey;
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

    /**
     * @return string the endpoint used
     */
    public function endpoint()
    {
        return $this->endpoint;
    }

    public function url()
    {
        return $this->endpointUrlMap[$this->endpoint] .
            "?key={$this->apikey}&" .
            http_build_query($this->params());
    }

    public function run()
    {
        if (Cache::has($this->cacheKey())) {
            $this->jsonResult = json_decode(
                Cache::get($this->cacheKey()),
                true
            );
            dump('From cache', __FILE__ . '-' . __FUNCTION__);
            return $this;
        }

        $jsonResult = Query::create($this->url())
            ->run()
            ->results();

        Cache::put($this->cacheKey(), $jsonResult, now()->addDays());
        $this->jsonResult = json_decode($jsonResult, true);
        return $this;
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

    public function responseKind()
    {
        return $this->jsonResult['kind'];
    }

    public function totalResults()
    {
        return $this->jsonResult['pageInfo']['totalResults'];
    }

    public function resultsPerPage()
    {
        return $this->jsonResult['pageInfo']['resultsPerPage'];
    }

    public function items()
    {
        return $this->jsonResult['items'];
    }

    public function partParams()
    {
        return $this->partParams;
    }

    protected function cacheKey()
    {
        $separator = '_';

        return 'youtube' .
            $separator .
            $this->endpoint() .
            $separator .
            http_build_query($this->params(), null, $separator);
    }
}
