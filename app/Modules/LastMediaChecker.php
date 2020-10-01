<?php

namespace App\Modules;

use App\Channel;
use App\Factories\YoutubeLastVideoFactory;
use App\Media;
use Carbon\Carbon;

class LastMediaChecker
{
    public const NB_HOURS_AGO = 6;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $media */
    protected $media;

    /** @var Carbon\Carbon $someHoursAgo some hours ago */
    protected $someHoursAgo;

    /** @var array $lastMediaFromYoutube */
    protected $lastMediaFromYoutube;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
        $this->someHoursAgo = Carbon::now()->subHours(self::NB_HOURS_AGO);
        $this->lastMediaFromYoutube = YoutubeLastVideoFactory::forChannel($this->channel->channel_id)->lastMedia();
        $this->media = Media::byMediaId($this->lastMediaFromYoutube['media_id']);
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    /**
     * determine if last media should be grabbed
     */
    public function shouldMediaBeingGrabbed(): bool
    {
        /** if already grabbed return false */
        if ($this->isTheMediaGrabbed()) {
            return false;
        }

        /**
         * To raise an alert media should
         * - have been published long ago
         * - not being filtered by tag
         * - not being filtered by date
         */

        if (
            $this->isMediaExcludedByTag() === true ||
            $this->isMediaExcludedByDate() === true
            ) {
            /** filtered => no alert should not been grabbed */
            return false;
        }

        if ($this->hasMediaBeenPublishedRecently() === true) {
            /** media is too recent to be already processed */
            return false;
        }

        return true;
    }

    public function isMediaExcludedByTag(): bool
    {
        // this media has no tag so there is no filtering to apply
        if (!count($this->lastMediaFromYoutube['tags'])) {
            return false;
        }

        return !$this->channel->areTagsAccepted($this->lastMediaFromYoutube['tags']);
    }

    public function isMediaExcludedByDate(): bool
    {
        return !$this->channel->isDateAccepted($this->lastMediaFromYoutube['published_at']);
    }

    /**
     * check if media has been published recently.
     *
     * @return bool
     */
    public function hasMediaBeenPublishedRecently(): bool
    {
        return $this->lastMediaFromYoutube['published_at']->isAfter($this->someHoursAgo);
    }

    public function isMediaBeenPublishedSomeHoursAgo()
    {
        return !$this->hasMediaBeenPublishedRecently();
    }

    /**
     * check if media has been created in DB and grabbed
     *
     * @return bool
     */
    public function isTheMediaGrabbed(): bool
    {
        if ($this->media === null) {
            return false;
        }

        return $this->media->hasBeenGrabbed();
    }
}
