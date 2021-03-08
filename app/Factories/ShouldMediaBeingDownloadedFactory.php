<?php

namespace App\Factories;

use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaAlreadyGrabbedException;
use App\Exceptions\MediaIsTooOldException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Media;
use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\Log;

/**
 * Check if a media is eligible for download.
 * Check if :
 * - video is processed on youtube
 * - video is passing filters (date and tags)
 */
class ShouldMediaBeingDownloadedFactory
{
    /** @var \App\Media $media */
    protected $media;

    private function __construct(Media $media)
    {
        $this->media = $media;
    }

    public static function create(Media $media)
    {
        return new static($media);
    }

    /**
     * check if media is eligible for download.
     * check filters and date.
     *
     * @throws MediaAlreadyGrabbedException
     * @throws YoutubeMediaIsNotAvailableException
     * @throws MediaIsTooOldException
     * @throws DownloadMediaTagException
     */
    public function check(): bool
    {
        /** if already grabbed return false */
        if ($this->isMediaAlreadyGrabbed()) {
            $message = "Media {$this->media->media_id} already grabbed for {$this->media->channel->nameWithId()}. No alert to send.";
            Log::notice($message);
            throw new MediaAlreadyGrabbedException($message);
        }

        $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

        /** is video downladable (not upcoming and processed) */
        if (!$youtubeVideo->isAvailable()) {
            $message = "The video {$this->media->media_id} for {$this->media->channel->nameWithId()} is not available yet. 'upcoming' live or not yet 'processed'.";
            Log::notice($message);
            throw new YoutubeMediaIsNotAvailableException($message);
        }

        /** check if media is not too old */
        if (!$this->media->channel->isDateAccepted($this->media->published_at)) {
            $message = "The video {$this->media->media_id} for {$this->media->channel->nameWithId()} is too old to be downloaded.";
            Log::notice($message);
            throw new MediaIsTooOldException($message);
        }

        /** if media has a tag, is it downladable */
        if (!$this->media->channel->areTagsAccepted($youtubeVideo->tags())) {
            $message = 'Media tags ' . implode(',', $youtubeVideo->tags()) .
                    " are not in allowed tags {$this->media->channel->accept_video_by_tag} for {$this->media->channel->nameWithId()}.";
            Log::notice($message);
            throw new DownloadMediaTagException($message);
        }

        return true;
    }

    public function isMediaAlreadyGrabbed(): bool
    {
        return $this->media->hasBeenGrabbed();
    }
}
