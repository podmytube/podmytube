<?php

namespace App\Youtube;

use App\Exceptions\YoutubeNoApikeyWasSetException;
use App\Exceptions\YoutubeNoPartParamException;
use App\Interfaces\QuotasCalculator;
use App\Traits\YoutubeEndpoints;

/**
 * Youtube Data API quota calculator.
 */
class YoutubeQuotas implements QuotasCalculator
{
    use YoutubeEndpoints;

    /** @var array $quotaConsumed */
    protected $quotaConsumed = [];
    /** @var array $this->queryParams query string as array */
    protected $queryParams = [];
    /** @var string $endpoint endpoint used (path) */
    protected $endpoint;
    /** @var string $apikeyUsed */
    protected $apikeyUsed;

    /**
     * classic constructor.
     * Do not mix create and new YoutubeQuota
     *
     * @param string endpoint to be used
     * @param string partParams used to collect some specific data
     *
     * @return YoutubeQuotas object
     */
    public function __construct(array $urls)
    {
        foreach ($urls as $url) {
            $this->calculateQuotaConsumed($url);
        }
    }

    public static function forUrls(...$params)
    {
        return new static(...$params);
    }

    protected function calculateQuotaConsumed(string $url)
    {
        // parsing the url
        $parsedUrl = parse_url($url);

        $this->endpoint = $parsedUrl['path'];
        $this->checkEndpoint($this->endpoint);

        // parsing the query string
        parse_str($parsedUrl['query'], $this->queryParams);

        // checking api key used for this query
        $this->checkApikey();

        if (!isset($this->quotaConsumed[$this->apikeyUsed])) {
            $this->quotaConsumed[$this->apikeyUsed] = 0;
        }

        $this->quotaConsumed[$this->apikeyUsed] += $this->baseQuotaCost(
            $this->endpoint
        );

        $this->partsParamsCosts();
    }

    protected function partsParamsCosts()
    {
        if (!isset($this->queryParams['part'])) {
            throw new YoutubeNoPartParamException(
                'No part params have been set for this query.'
            );
        }

        $partParams = explode(',', $this->queryParams['part']);
        foreach ($partParams as $partParam) {
            $this->quotaConsumed[$this->apikeyUsed] += $this->partQuotaCost(
                $this->endpoint,
                $partParam
            );
        }
    }

    protected function checkApikey()
    {
        if (!isset($this->queryParams['key'])) {
            throw new YoutubeNoApikeyWasSetException('');
        }
        $this->apikeyUsed = $this->queryParams['key'];
        return true;
    }

    public function quotaConsumed(): array
    {
        return $this->quotaConsumed;
    }
}
