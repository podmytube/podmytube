<?php

namespace App\Factories;

use App\Exceptions\YoutubeChannelHasNoVideoException;
use App\Interfaces\QuotasConsumer;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use App\Youtube\YoutubeVideo;

class YoutubeLastVideoFactory implements QuotasConsumer
{
    public const SCRIPT_NAME = 'YoutubeLastVideoFactory.php';

    /** @var string $channel_id */
    protected $channel_id;

    /** @var array $lastMedia */
    protected $lastMedia = [];

    /** @var array $queries */
    protected $queries = [];

    private function __construct(string $channel_id)
    {
        $this->channel_id = $channel_id;
        $this->obtainLastMedia();
        $this->obtainTagsForMedia();
        $this->saveQuotaConsumption();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    protected function obtainLastMedia()
    {
        $factory = YoutubeChannelVideos::forChannel($this->channel_id, 1);

        if (!count($factory->videos())) {
            throw new YoutubeChannelHasNoVideoException("Channel {$this->channel_id} has no video. Strange you should contact them.");
        }
        $this->lastMedia = $factory->videos()[0];
        $this->queries = array_merge($this->queries, $factory->queriesUsed());
    }

    protected function obtainTagsForMedia()
    {
        $factory = YoutubeVideo::forMedia(
            $this->lastMedia['media_id'],
            ['id', 'status']
        );
        $this->lastMedia['tags'] = $factory->tags();
        $this->queries = array_merge($this->queries, $factory->queriesUsed());
    }

    public function quotasConsumed(): array
    {
        return YoutubeQuotas::forUrls($this->queries)->quotaConsumed();
    }

    public function queriesUsed(): array
    {
        return $this->queries;
    }

    /**
     * this function will return the result of the collect about the channel.
     * last media array will contain :
     * - media_id (string)
     * - playlist_id (string)
     * - title (string)
     * - description (string)
     * - published_at (Carbon object)
     * - tags (array)
     *
     * @return array
     */
    public function lastMedia()
    {
        return $this->lastMedia;
    }

    protected function saveQuotaConsumption()
    {
        return Quota::saveScriptConsumption(self::SCRIPT_NAME, $this->quotasConsumed());
    }
}
