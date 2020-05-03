<?php

namespace App\Services;

use App\Exceptions\YoutubeApiInvalidChannelIdException;
use App\Exceptions\YoutubeQueryFailureException;
use Madcoda\Youtube\Youtube;

/**
 * This class is there to get some basic informations about one channel.
 * It will be used to validate a registering channel.
 */
class YoutubeChannelCheckingService
{
    protected $youtubeObj;
    protected $youtubeChannelInformations;

    private function __construct(Youtube $youtube, string $channelId)
    {
        $this->youtubeObj = $youtube;

        /**
         * Getting channel informations
         */
        try {
            $result = $this->youtubeObj->getChannelById($channelId);
        } catch (\Exception $exception) {
            throw new YoutubeQueryFailureException(
                "Youtube query has failed with message : {$exception->getMessage()}"
            );
        }

        if ($result === false) {
            throw new YoutubeApiInvalidChannelIdException(
                "Cannot get channel information for this channel {$channelId}"
            );
        }
        $this->youtubeChannelInformations = $result;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    /**
     * This function wil extract the name.
     *
     * @return String channel name
     */
    public function getChannelName(): string
    {
        return $this->youtubeChannelInformations->snippet->title;
    }
}
