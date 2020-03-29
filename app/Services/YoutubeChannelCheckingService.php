<?php

namespace App\Services;

use App\ApiKey;
use App\Exceptions\YoutubeApiInvalidChannelIdException;
use App\Exceptions\YoutubeApiInvalidKeyException;

/**
 * This class is there to get some basic informations about one channel.
 * It will be used to validate a registering channel.
 */
class YoutubeChannelCheckingService
{
    protected static $apiKey;
    protected static $youtubeObj;
    protected static $youtubeChannelInformations;

    /**
     * this function will grab the youtube api key and obtain some data from youtube about the channelId
     * @throws Exception 
     * @param String $channelId
     */
    protected static function init(String $channelId)
    {
        self::$apiKey = ApiKey::make()->getOne()->apikey;
        if (empty(self::$apiKey)) {
            throw new YoutubeApiInvalidKeyException("YOUTUBE_API_KEY is not set in tne env file");
        }

        /**
         * Setting api key
         */
        self::$youtubeObj = new \Madcoda\Youtube\Youtube(array('key' => self::$apiKey));

        /**
         * Getting channel informations
         */
        $result = self::$youtubeObj->getChannelById($channelId);
        if ($result === false) {
            throw new YoutubeApiInvalidChannelIdException("Cannot get channel information for this channel {{$channelId}}");
        }

        self::$youtubeChannelInformations = $result;
    }

    /**
     * This function wil extract the name.
     * @param String $channelId
     * @return String channel name
     */
    public static function getChannelName(String $channelId)
    {
        try {

            if (empty(self::$youtubeChannelInformations)) {
                self::init($channelId);
            }

            return self::$youtubeChannelInformations->snippet->title;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
