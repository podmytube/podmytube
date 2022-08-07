<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\YoutubeNoResultsException;
use App\Interfaces\QuotasConsumer;
use App\Models\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use App\Youtube\YoutubeVideo;

class YoutubeLastVideoFactory implements QuotasConsumer
{
    public const SCRIPT_NAME = 'YoutubeLastVideoFactory.php';

    /** @var array */
    protected $lastMedia = [];

    /** @var array */
    protected $queries = [];

    private function __construct(protected string $channel_id)
    {
        $this->obtainLastMedia();
        $this->obtainTagsForMedia();
        $this->saveQuotaConsumption();
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
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
     * - tags (array).
     */
    public function lastMedia(): array
    {
        return $this->lastMedia;
    }

    protected function obtainLastMedia(): void
    {
        $factory = YoutubeChannelVideos::forChannel($this->channel_id, 1);
        if (!count($factory->videos())) {
            throw new YoutubeNoResultsException("Channel {$this->channel_id} has no video. Strange you should contact them.");
        }
        $this->lastMedia = $factory->videos()[0];
        $this->queries = array_merge($this->queries, $factory->queriesUsed());
    }

    protected function obtainTagsForMedia(): void
    {
        $factory = YoutubeVideo::forMedia(
            $this->lastMedia['media_id'],
            ['id', 'status']
        );
        $this->lastMedia['tags'] = $factory->tags();
        $this->lastMedia['available'] = $factory->isAvailable();
        $this->queries = array_merge($this->queries, $factory->queriesUsed());
    }

    protected function saveQuotaConsumption()
    {
        return Quota::saveScriptConsumption(self::SCRIPT_NAME, $this->quotasConsumed());
    }
}
