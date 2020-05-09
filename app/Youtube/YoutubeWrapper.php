<?php

namespace App\Youtube;

use App\ApiKey;
use App\Channel;
use App\Exceptions\YoutubeApiInvalidChannelIdException;
use App\Exceptions\YoutubeQueryFailureException;
use Madcoda\Youtube\Youtube;

class YoutubeWrapper
{
    /** @var \App\ApiKey $apikey */
    protected $apikey;
    /** @var \App\Channel $channel */
    protected $channel;
    /** @var string $channelInformations json returned by youtube */
    protected $channelInformations;

    private function __construct(ApiKey $apikey, Channel $channel)
    {
        $this->apikey = $apikey;
        $this->channel = $channel;
        $this->youtube = new Youtube([
            'key' => $this->apikey->get(),
        ]);
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function run()
    {
        try {
            $result = $this->youtube->getChannelById(
                $this->channel->channelId()
            );
        } catch (\Exception $exception) {
            throw new YoutubeQueryFailureException(
                "Youtube query has failed with message : {$exception->getMessage()}"
            );
        }

        if ($result === false) {
            throw new YoutubeApiInvalidChannelIdException(
                "Cannot get channel information for this channel {$this->channel->channelId()}"
            );
        }

        $this->channelInformations = $result;
        return $this;
    }

    public function channelInformations()
    {
        return $this->channelInformations;
    }
}
