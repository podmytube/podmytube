<?php

namespace App\Modules;

use App\Channel;
use App\Factories\YoutubeLastVideoFactory;
use App\Media;
use App\Youtube\YoutubeChannelVideos;
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

    public static function for(...$params)
    {
        return new static(...$params);
    }

    public function shouldMediaBeingGrabbed(): bool
    {
        /** 
         * if media has been posted recently, 
         * if media is not filtered by tags
         * if media is not filtered by date
         * not necessary to raise an alert
         */
        if (
            $this->mediaHasBeenPublishedRecently() &&
            !$this->mediaIsExcludedByTag()
        ) {
            return false;
        }



        /** is it filtered by tags */
        if ($this->mediaIsExcludedByTag()) {
            return false;
        }

        /** if already grabbed return false */
        if ($this->isTheMediaGrabbed()) {
            return false;
        }

        return true;
    }

    public function mediaIsExcludedByTag(): bool
    {
        // this media has no tag so there is no filtering to apply
        if (!count($this->lastMediaFromYoutube['tags'])) {
            return false;
        }

        return !$this->channel->areTagsAccepted($this->lastMediaFromYoutube['tags']);
    }

    public function isMediaExcludedByDate(): bool
    {
        dump($this->lastMediaFromYoutube['published_at'], $this->channel->isDateAccepted($this->lastMediaFromYoutube['published_at']));
        return !$this->channel->isDateAccepted($this->lastMediaFromYoutube['published_at']);
    }


    /**
     * check if media has been published recently.
     * 
     * @return bool
     */
    public function mediaHasBeenPublishedRecently(): bool
    {
        return $this->lastMediaFromYoutube['published_at']->isAfter($this->someHoursAgo);
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

        return  $this->media->hasBeenGrabbed();
    }
}
