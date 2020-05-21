<?php

namespace App\Youtube;

class YoutubeChannel
{
    /** @var \App\Youtube\YoutubeCore $youtubeCore  */
    protected $youtubeCore;
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var array $results result of youtube query */
    protected $results;

    private function __construct(YoutubeCore $youtubeCore)
    {
        $this->youtubeCore = $youtubeCore;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function forChannel(string $channelId)
    {
        $this->channelId = $channelId;
        $this->results = $this->youtubeCore
            ->defineEndpoint('channels.list')
            ->addParts(['id', 'snippet'])
            ->addParams(['id' => $channelId])
            ->run()
            ->results();
        return $this;
    }

    protected function hasResult()
    {
        return $this->results['pageInfo']['totalResults'] <= 0;
    }

    public function exists()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->channelId === $this->results['items'][0]['id'];
    }

    public function name()
    {
        if ($this->hasResult()) {
            return false;
        }
        return $this->results['items'][0]['snippet']['title'];
    }
}
