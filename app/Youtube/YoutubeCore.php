<?php

namespace App\Youtube;

use App\ApiKey;
use App\Exceptions\YoutubeInvalidEndpointException;
use App\Modules\Query;
use App\Traits\YoutubeEndpoints;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

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
    /** @var array $jsonDecoded contain the whole response */
    protected $jsonDecoded = [];
    /** @var array $items contains only the items */
    protected $items = [];
    /** @var int $limit max number of items to get */
    protected $limit = 0;
    /** @var array $params query parameters */
    protected $params = [];
    /** @var array $partParams youtube part parameters */
    protected $partParams = [];

    private function __construct()
    {
        $this->apikey = $this->getApiKey();
        $this->params['part'] = [];
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    protected function getApiKey()
    {
        if (Config::has('apikey')) {
            return Config::get('apikey');
        }
        return (new ApiKey())->get();
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
        do {
            $rawResults = $this->getRawResults();
            $this->jsonDecoded = json_decode($rawResults, true);

            if (isset($this->jsonDecoded['items'])) {
                $this->items = array_merge(
                    $this->items,
                    $this->jsonDecoded['items']
                );
            }

            if (isset($this->jsonDecoded['nextPageToken'])) {
                $this->setPageToken($this->jsonDecoded['nextPageToken']);
            }
        } while ($this->doWeGetNextPage());

        return $this;
    }

    /**
     *
     */
    protected function doWeGetNextPage()
    {
        if ($this->limit > 0 && count($this->items()) > $this->limit) {
            return false;
        }
        if (!isset($this->jsonDecoded['nextPageToken'])) {
            return false;
        }
        return true;
    }

    public function setLimit(int $limit)
    {
        if ($limit > 0) {
            $this->limit = $limit;
        }
        return $this;
    }

    protected function getRawResults()
    {
        // get it from cache (if any)
        if (Cache::has($this->cacheKey())) {
            return Cache::get($this->cacheKey());
        }
        // querying api
        $rawResults = Query::create($this->url())
            ->run()
            ->results();
        // putting results in cache for next time
        Cache::put($this->cacheKey(), $rawResults, now()->addDays());
        return $rawResults;
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

    protected function setPageToken($pageToken)
    {
        $this->params['pageToken'] = $pageToken;
        return $this;
    }

    public function params()
    {
        $this->params['part'] = implode(',', $this->partParams());
        ksort($this->params);
        return $this->params;
    }

    public function clearParams()
    {
        $this->params = [];
        $this->partParams = [];
        return $this;
    }

    public function results()
    {
        return $this->jsonDecoded;
    }

    public function totalResults()
    {
        return $this->jsonDecoded['pageInfo']['totalResults'];
    }

    public function items()
    {
        return $this->items;
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
