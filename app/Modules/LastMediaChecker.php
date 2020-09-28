<?php

namespace App\Modules;

use App\Channel;
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
        $this->lastMediaFromYoutube = (new YoutubeChannelVideos())
            ->forChannel($this->channel->channel_id, 1)
            ->lastVideo();
        dd($this->lastMediaFromYoutube);

        $this->media = Media::find($this->lastMediaFromYoutube['media_id']);
    }

    public static function for(...$params)
    {
        return new static(...$params);
    }

    public function shouldItBeGrabbed(): bool
    {
        /** has it been posted recently */
        if ($this->mediaHasBeenPublishedRecently()) {
            return false;
        }

        /** is it filtered */
        if ($this->mediaIsExcludedByTag()) {
            return false;
        }

        /** if already grabbed return false */
        if ($this->isTheMediaGrabbed($this->lastMediaMediaFromYoutube['media_id'])) {
            return false;
        }

        return true;
    }

    protected function mediaIsExcludedByTag()
    {
        if (!$this->channel->hasFilter()) {
            return false;
        }

        /**
         * if channel want to exclude old videos and this media is before
         */
        if (
            $this->channel->reject_video_too_old !== null &&
            $this->lastMediaFromYoutube['published_at']->isAfter($this->channel->reject_video_too_old)
        ) {
            return false;
        }


        /**
         * if channel filtering only some tag
         */
        if ($this->channel->accept_video_by_tag !== null) {
            return false;
        }
        return true;
    }

    /**
     * check if media has been published recently.
     * 
     * @return bool
     */
    protected function mediaHasBeenPublishedRecently(): bool
    {
        return $this->lastMediaFromYoutube['published_at']->isAfter($this->someHoursAgo);
    }

    /** 
     * check if media has been created in DB and grabbed 
     * 
     * @return bool
     */
    public function isTheMediaGrabbed(string $mediaId): bool
    {
        if ($this->media === null) {
            return false;
        }

        return  $this->media->hasBeenGrabbed();
    }
}
