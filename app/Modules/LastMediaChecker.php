<?php

declare(strict_types=1);

namespace App\Modules;

use App\Channel;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaAlreadyGrabbedException;
use App\Exceptions\MediaIsTooOldException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Factories\ShouldMediaBeingDownloadedFactory;
use App\Factories\YoutubeLastVideoFactory;
use App\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LastMediaChecker
{
    public const NB_HOURS_AGO = 6;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    /** @var Carbon\Carbon some hours ago */
    protected $someHoursAgo;

    /** @var array */
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
     * determine if last media should be grabbed.
     */
    public function shouldMediaBeingGrabbed(): bool
    {
        if (true === $this->hasMediaBeenPublishedRecently()) {
            // media is too recent to be already processed
            Log::notice(
                "Last media {$this->lastMediaFromYoutube['media_id']} has been published recently for {$this->channel->nameWithId()}. \\
                No alert to send."
            );

            return false;
        }

        if (null === $this->media) {
            Log::notice(
                "Media {$this->lastMediaFromYoutube['media_id']} published more than ".self::NB_HOURS_AGO." hours ago is still unknown \\
                for {$this->channel->nameWithId()}. Sending alert !"
            );

            return true;
        }

        try {
            ShouldMediaBeingDownloadedFactory::create($this->media)->check();
        } catch (
            MediaAlreadyGrabbedException |
            YoutubeMediaIsNotAvailableException |
            MediaIsTooOldException |
            DownloadMediaTagException $exception
            ) {
            return false;
        }

        return true;
    }

    /**
     * check if media has been published recently.
     */
    public function hasMediaBeenPublishedRecently(): bool
    {
        return $this->lastMediaFromYoutube['published_at']->isAfter($this->someHoursAgo);
    }
}
