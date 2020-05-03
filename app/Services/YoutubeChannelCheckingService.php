<?php

namespace App\Services;

use App\ApiKey;
use App\Exceptions\YoutubeApiInvalidChannelIdException;

/**
 * This class is there to get some basic informations about one channel.
 * It will be used to validate a registering channel.
 */
class YoutubeChannelCheckingService
{
    protected $apiKey;
    protected $youtubeObj;
    protected $youtubeChannelInformations;

    private function __construct(ApiKey $apikey, string $channelId)
    {
        $this->apiKey = $apikey;
        
        /**
         * Setting api key
         */
        $this->youtubeObj = new \Madcoda\Youtube\Youtube([
            'key' => $this->apiKey->getOne()->apiKey,
        ]);

        /**
         * Getting channel informations
         */
        $result = $this->youtubeObj->getChannelById($channelId);
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
