<?php

namespace App\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeInvalidPartParamException;
use App\Interfaces\QuotasCalculator;
use App\Interfaces\QuotasConsumer;
use App\Traits\YoutubeEndpoints;

/**
 * Youtube Data API quota calculator.
 *
 * @version 1
 */
class YoutubeQuotas implements QuotasCalculator
{
    use YoutubeEndpoints;

    protected $endpointBaseQuotaCostMap = [
        'channels.list' => 1,
        'playlistItems.list' => 1,
        'playlists.list' => 1,
        'search.list' => 100,
        'videos.list' => 1,
    ];

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
    public function __construct()
    {
    }

    public function addQuotaConsumer(QuotasConsumer $quotaConsumer)
    {
        $this->quotaConsumer = $quotaConsumer;
        if (
            !isset(
                $this->endpointBaseQuotaCostMap[
                    $this->quotaConsumer->endpoint()
                ]
            )
        ) {
            throw new YoutubeInvalidEndpointException(
                "Unknown endpoint {$this->quotaConsumer->endpoint()}."
            );
        }
        /**
         * each call has a min quota cost
         */
        $this->quotaConsumed +=
            $this->endpointBaseQuotaCostMap[$this->quotaConsumer->endpoint()];

        $this->quotaCalculator();
        return $this;
    }

    /**
     * main calculator.
     *
     * @param array params and the quota they consume
     */
    protected function quotaCalculator()
    {
        $scoreByPartMap = $this->getEndpointMap(
            $this->quotaConsumer->endpoint()
        );

        foreach ($this->quotaConsumer->partParams() as $part) {
            if (!isset($scoreByPartMap[$part])) {
                throw new YoutubeInvalidPartParamException(
                    "This part param {$part} is invalid",
                    1
                );
            }
            $this->quotaConsumed += $scoreByPartMap[$part];
        }
    }

    /**
     * getter quotas.
     *
     * @return int quota consumed on query(ies)
     */
    public function quotas(): int
    {
        return $this->quotaConsumed;
    }
}
