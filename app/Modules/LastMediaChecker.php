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

    protected ?Media $media;

    protected Carbon $someHoursAgo;

    protected array $lastMediaFromYoutube;

    private function __construct(protected Channel $channel)
    {
        $this->someHoursAgo = now()->subHours(self::NB_HOURS_AGO);
    }

    public static function forChannel(...$params)
    {
        return new static(...$params);
    }

    public function run(): self
    {
        $this->lastMediaFromYoutube = YoutubeLastVideoFactory::forChannel($this->channel->channel_id)->lastMedia();
        $this->media = Media::byMediaId($this->lastMediaFromYoutube['media_id']);

        return $this;
    }

    /**
     * determine if last media should be grabbed.
     */
    public function shouldMediaBeingGrabbed(): bool
    {
        if ($this->hasMediaBeenPublishedRecently() === true) {
            // media is too recent to be already processed
            Log::debug(
                "Last media {$this->lastMediaFromYoutube['media_id']} has been published recently for {$this->channel->nameWithId()}. \\
                No alert to send."
            );

            return false;
        }

        if ($this->media === null) {
            Log::debug(
                "Media {$this->lastMediaFromYoutube['media_id']} published more than " . self::NB_HOURS_AGO . " hours ago is still unknown \\
                for {$this->channel->nameWithId()}. Sending alert !"
            );

            return true;
        }

        try {
            ShouldMediaBeingDownloadedFactory::create($this->media)->check();
        } catch (
            MediaAlreadyGrabbedException|
            YoutubeMediaIsNotAvailableException|
            MediaIsTooOldException|
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

    public function lastMediaFromYoutube(): array
    {
        return $this->lastMediaFromYoutube;
    }
}
