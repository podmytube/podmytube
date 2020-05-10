<?php

namespace App\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeInvalidPartParamException;
use App\Traits\YoutubeEndpoints;

/**
 * Youtube Data API quota calculator.
 *
 * @version 1
 */
class YoutubeQuotas
{
    use YoutubeEndpoints;

    protected $endpointBaseQuotaCostMap = [
        'videos.list' => 1,
        'search.list' => 100,
        'channels.list' => 1,
        'playlists.list' => 1,
        'playlistItems.list' => 1,
    ];

    protected $quotaUsed = 0;

    /**
     * classic constructor.
     * Do not mix create and new YoutubeQuota
     *
     * @param string endpoint to be used
     * @param string partParams used to collect some specific data
     *
     * @return YoutubeQuotas object
     */
    public function __construct(YoutubeCore $youtubeCore)
    {
        $this->youtubeCore = $youtubeCore;
        if (
            !isset(
                $this->endpointBaseQuotaCostMap[$this->youtubeCore->endpoint()]
            )
        ) {
            throw new YoutubeInvalidEndpointException(
                "Unknown endpoint {$this->youtubeCore->endpoint()}."
            );
        }
        /**
         * each call has a min quota cost
         */
        $this->quotaUsed +=
            $this->endpointBaseQuotaCostMap[$this->youtubeCore->endpoint()];

        $this->quotaCalculator();
    }

    /**
     * Static constructor
     *
     * @return YoutubeQuotas object
     */
    public static function init(...$params)
    {
        return new static(...$params);
    }

    /**
     * main calculator.
     *
     * @param array params and the quota they consume
     */
    protected function quotaCalculator()
    {
        $scoreByPartMap = $this->getEndpointMap($this->youtubeCore->endpoint());

        foreach ($this->youtubeCore->partParams() as $part) {
            if (!isset($scoreByPartMap[$part])) {
                throw new YoutubeInvalidPartParamException(
                    "This part param {$part} is invalid",
                    1
                );
            }
            $this->quotaUsed += $scoreByPartMap[$part];
        }
    }

    /**
     * getter quotaUsed.
     *
     * @return int quota used on query(ies)
     */
    public function quotaUsed()
    {
        return $this->quotaUsed;
    }
}
