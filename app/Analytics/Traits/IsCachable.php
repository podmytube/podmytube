<?php

declare(strict_types=1);

namespace App\Analytics\Traits;

use App\Models\Channel;
use App\Models\Media;
use Illuminate\Support\Facades\Cache;

trait IsCachable
{
    /*
    |--------------------------------------------------------------------------
    | has
    |--------------------------------------------------------------------------
    */
    public function hasChannelBeenMet(string $channelId)
    {
        return Cache::has($this->knownChannelCacheKey($channelId));
    }

    public function hasMediaBeenMet(string $mediaId)
    {
        return Cache::has($this->knownMediaCacheKey($mediaId));
    }

    /*
    |--------------------------------------------------------------------------
    | recover
    |--------------------------------------------------------------------------
    */
    protected function recoverKnownChannelModel(string $channelId): Channel
    {
        return Cache::get($this->knownChannelCacheKey($channelId));
    }

    protected function recoverKnownMediaModel(string $mediaId): Media
    {
        return Cache::get($this->knownMediaCacheKey($mediaId));
    }

    /*
    |--------------------------------------------------------------------------
    | mark & store result
    |--------------------------------------------------------------------------
    */
    protected function markChannelAsMetAndStoreResult(string $channelId, ?Channel $channel): void
    {
        Cache::put($this->knownChannelCacheKey($channelId), $channel);
    }

    protected function markMediaAsMetAndStoreResult(string $mediaId, ?Media $media): void
    {
        Cache::put($this->knownChannelCacheKey($mediaId), $media);
    }

    /*
    |--------------------------------------------------------------------------
    | cache keys
    |--------------------------------------------------------------------------
    */
    protected function knownChannelCacheKey(string $channelId): string
    {
        return $this->cachePrefix . $this->cacheKeySeparator . 'CHANNEL' . $this->cacheKeySeparator . $channelId;
    }

    protected function knownMediaCacheKey(string $mediaId): string
    {
        return $this->cachePrefix . $this->cacheKeySeparator . 'MEDIA' . $this->cacheKeySeparator . $mediaId;
    }

    protected function incrementDownloads(string $logDay, string $channelId, string $mediaId): void
    {
        Cache::increment(
            $this->cachePrefix . $this->cacheKeySeparator
            . 'DOWNLOADS' . $this->cacheKeySeparator
            . $logDay . $this->cacheKeySeparator
            . $channelId . $this->cacheKeySeparator
            . $mediaId
        );
    }
}
