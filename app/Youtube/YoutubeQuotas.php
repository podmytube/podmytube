<?php

namespace App\Youtube;

use App\Interfaces\QuotasCalculator;
use App\Traits\YoutubeEndpoints;

/**
 * Youtube Data API quota calculator.
 */
class YoutubeQuotas implements QuotasCalculator
{
    use YoutubeEndpoints;

    /** @var int $quotaConsumed */
    protected $quotaConsumed = 0;

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
            $this->quotaConsumed += $this->calculateQuotaConsumed($url);
        }
    }

    public static function forUrls(...$params)
    {
        return new static(...$params);
    }

    protected function calculateQuotaConsumed(string $url): int
    {
        // parsing the url
        $parsedUrl = parse_url($url);

        $endpoint = $parsedUrl['path'];
        $this->checkEndpoint($endpoint);
        $quotaCost = $this->baseQuotaCost($endpoint);

        //parsing the query string
        parse_str($parsedUrl['query'], $queryParams);

        if (isset($queryParams['part'])) {
            $partParams = explode(',', $queryParams['part']);
            foreach ($partParams as $part) {
                $quotaCost += $this->partQuotaCost($endpoint, $part);
            }
        }
        return $quotaCost;
    }

    public function quotaConsumed(): int
    {
        return $this->quotaConsumed;
    }
}
