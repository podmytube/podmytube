<?php

namespace App\Youtube;

use App\ApiKey;
use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeNoResultsException;
use App\Exceptions\YoutubeQueryFailureException;
use App\Interfaces\QuotasConsumer;
use App\Modules\Query;
use App\Traits\YoutubeEndpoints;
use Illuminate\Support\Facades\Cache;

abstract class YoutubeCore implements QuotasConsumer
{
    use YoutubeEndpoints;

    public const YOUTUBE_API_BASE_URL = 'https://www.googleapis.com';

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
    /** @var array $queries list of valid queries used */
    protected $queries = [];

    public function __construct()
    {
        $this->apikey = ApiKey::getOne();
        $this->params['part'] = [];
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function apikey()
    {
        return $this->apikey;
    }

    public function defineEndpoint(string $endpoint)
    {
        $this->checkEndpoint($endpoint);
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return string the endpoint used
     */
    public function endpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Will return the youtube api url to query.
     *
     * @return string $url to query.
     */
    public function url(): string
    {
        return self::YOUTUBE_API_BASE_URL .
            $this->endpoint .
            "?key={$this->apikey}&" .
            http_build_query($this->params());
    }

    /**
     * run the query(ies) and get results.
     */
    public function run(): self
    {
        do {
            $rawResults = $this->getRawResults();

            /**
             * convert json string into a hash table.
             */
            $this->jsonDecoded = json_decode($rawResults, true);

            if (isset($this->jsonDecoded['error'])) {
                throw new YoutubeQueryFailureException($this->jsonDecoded['error']['message'], $this->jsonDecoded['error']['code']);
            }

            if (!isset($this->jsonDecoded['items'])) {
                throw new YoutubeNoResultsException('No results for ' . $this->url());
            }

            /**
             * adding them to previous results
             */
            $this->items = array_merge($this->items, $this->jsonDecoded['items']);

            /**
             * if response is multi page, prepare next youtube query.
             */
            if (isset($this->jsonDecoded['nextPageToken'])) {
                $this->setPageToken($this->jsonDecoded['nextPageToken']);
            }
        } while ($this->doWeGetNextPage());

        return $this;
    }

    /**
     * return if we are qyuerying youtube api next page.
     * According to an eventual limit set or the presence of a nextPageToken
     * in the response we are going to make another youtube api query
     */
    protected function doWeGetNextPage(): bool
    {
        if ($this->limit > 0 && $this->nbItemsGrabbed() >= $this->limit) {
            return false;
        }
        if (!isset($this->jsonDecoded['nextPageToken'])) {
            return false;
        }
        return true;
    }

    protected function nbItemsGrabbed()
    {
        return count($this->items());
    }

    /**
     * Define a limit.
     *
     * @param int $limit maximum number of items we need. 0=unlimited.
     */
    public function setLimit(int $limit)
    {
        if ($limit >= 0) {
            $this->limit = $limit;
        }
        return $this;
    }

    /**
     * Return the raw json result.
     * May come from the cache of from youtube api.
     */
    protected function getRawResults(): string
    {
        // get it from cache (if any)
        if (Cache::has($this->cacheKey())) {
            return Cache::get($this->cacheKey());
        }
        // querying api
        $rawResults = Query::create($this->url())
            ->run()
            ->results();
        // adding url to the list of queries used
        $this->queries[] = $this->url();
        // putting results in cache for next time
        Cache::put($this->cacheKey(), $rawResults, now()->addHours(6));
        return $rawResults;
    }

    /**
     * Tell if yes or no this api request has results.
     * I was testing pageInfo.totalResults but this key disappeared recently
     * from queries answer.
     *
     * @return bool true if query has results.
     */
    protected function hasResult(): bool
    {
        return $this->nbItemsGrabbed() > 0;
    }

    /**
     * Add some part params to the query.
     * Because part params are different for every endpoint,
     * this one should be set before.
     *
     * @param array $parts
     */
    public function addParts(array $parts): self
    {
        if (!isset($this->endpoint)) {
            throw new YoutubeInvalidEndpointException(
                'Endpoint not defined. You should set one before.'
            );
        }
        $this->partParams = array_unique(
            array_merge(
                $this->partParams,
                array_filter($parts, function ($partToCheck) {
                    return in_array(
                        $partToCheck,
                        $this->endpointParts($this->endpoint)
                    );
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
        return 'youtube' . $separator . $this->endpoint() . $separator . http_build_query($this->params(), '', $separator);
    }

    public function quotasUsed(): int
    {
        return $this->quotaCalculator->addQuotaConsumer($this)->quotas();
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }
}
