<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeNoResultsException;
use App\Interfaces\QuotasConsumer;
use App\Models\ApiKey;
use App\Traits\YoutubeEndpoints;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class YoutubeCore implements QuotasConsumer
{
    use YoutubeEndpoints;

    public const YOUTUBE_API_BASE_URL = 'https://www.googleapis.com';
    public const CHANNELS_ENDPOINT = '/youtube/v3/channels';
    public const PLAYLIST_ITEMS_ENDPOINT = '/youtube/v3/playlistItems';
    public const PLAYLISTS_ENDPOINT = '/youtube/v3/playlists';
    public const SEARCH_ENDPOINT = '/youtube/v3/search';
    public const VIDEOS_ENDPOINT = '/youtube/v3/videos';

    protected string $apikey;

    protected Response $response;
    protected string $endpoint;
    protected array $nextPageTokens = [];

    protected array $items = [];

    /** @var int max number of items to get */
    protected int $limit = 0;

    /** @var array query parameters */
    protected array $params = [];

    /** @var array youtube part parameters */
    protected array $partParams = [];

    /** @var array list of valid queries used */
    protected array $queries = [];

    protected string $nextPageToken;

    /**
     * passing optional Apikey is a way to fix this old getOne crap
     * I made long ago.
     */
    public function __construct(?ApiKey $apiKeyModel = null)
    {
        if ($apiKeyModel === null) {
            $this->apikey = ApiKey::getOne();
        } else {
            $this->apikey = $apiKeyModel->apikey;
        }
        $this->params['part'] = [];
    }

    public static function init(...$params): static
    {
        return new static(...$params);
    }

    public function apikey(): string
    {
        return $this->apikey;
    }

    public function defineEndpoint(string $endpoint): static
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
     * @return string $url to query
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
    public function run(): static
    {
        do {
            $this->response = Http::timeout(10)->get($this->url());
            // ray($this->url(), $this->response->json(), 'nextpageToken : ' . $this->response->json('nextPageToken'));
            // adding url to the list of queries used
            $this->queries[] = $this->url();

            if (
                $this->response->failed()
                || $this->response->json('error') !== null
            ) {
                /*
                 * Sometimes, for the same query Youtube is returning a json with
                 * an error and sometimes he is returning a json with no errors and empty
                 * item array, so I cannot throw differrent Exception for these 2 kinds of
                 * exceptions
                 */
                $exception = new YoutubeGenericErrorException();
                $exception->addInformations('url used ' . $this->url());
                $exception->addInformations('youtube error message ' . $this->response->json('error.message'));
                $exception->addInformations('youtube error code ' . $this->response->json('error.code'));

                throw $exception;
            }

            throw_if(
                $this->response->json('items') === null,
                new YoutubeNoResultsException('No results for ' . $this->url())
            );

            // adding them to previous results
            $this->items = array_merge($this->items, $this->response->json('items'));
        } while ($this->doWeGetNextPage());

        return $this;
    }

    /**
     * Define a limit.
     *
     * @param int $limit maximum number of items we need. 0=unlimited.
     */
    public function setLimit(int $limit): static
    {
        if ($limit >= 0) {
            $this->limit = $limit;
        }

        return $this;
    }

    /**
     * Add some part params to the query.
     * Because part params are different for every endpoint,
     * this one should be set before.
     */
    public function addParts(array $parts): static
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

    public function addParams(array $attributes = []): static
    {
        $this->params = array_merge($this->params, $attributes);

        return $this;
    }

    public function params(): array
    {
        $this->params['part'] = implode(',', $this->partParams());
        ksort($this->params);

        return $this->params;
    }

    public function results(): array
    {
        return $this->response->json();
    }

    public function totalResults(): int
    {
        return $this->response->json('pageInfo.totalResults');
    }

    public function items(): array
    {
        return $this->items;
    }

    public function partParams(): array
    {
        return $this->partParams;
    }

    public function quotasUsed(): int
    {
        return $this->quotaCalculator->addQuotaConsumer($this)->quotas();
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }

    public function channelId(): string
    {
        return $this->items[0]['snippet']['channelId'];
    }

    /**
     * return if we are qyuerying youtube api next page.
     * According to an eventual limit set or the presence of a nextPageToken
     * in the response we are going to make another youtube api query.
     */
    protected function doWeGetNextPage(): bool
    {
        if ($this->limit > 0 && $this->nbItemsGrabbed() >= $this->limit) {
            // we grabbed more items we need => stop
            Log::debug('we grabbed more items we need => stop. limit was ' . $this->limit . ' obtained ' . $this->nbItemsGrabbed());

            return false;
        }

        if (in_array($this->response->json('nextPageToken'), $this->nextPageTokens)) {
            // we already used this nextPageToken => stop
            Log::debug('we already used this nextPageToken => stop');

            return false;
        }

        if ($this->response->json('nextPageToken') !== null) {
            // storing nextPageToken used
            $this->nextPageTokens[] = $this->response->json('nextPageToken');
            $this->params['pageToken'] = $this->response->json('nextPageToken');
        }

        Log::debug('nextPageToken ? ' . $this->response->json('nextPageToken') !== null);
        // if there a nextPageToken in the response => continue, else => stop
        return $this->response->json('nextPageToken') !== null;
    }

    protected function nbItemsGrabbed(): int
    {
        return count($this->items());
    }

    /**
     * Tell if yes or no this api request has results.
     * I was testing pageInfo.totalResults but this key disappeared recently
     * from queries answer.
     *
     * @return bool true if query has results
     */
    protected function hasResult(): bool
    {
        return $this->nbItemsGrabbed() > 0;
    }
}
