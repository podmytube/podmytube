<?php

namespace App\Factories;

use App\Exceptions\DownloadMediaTagException;
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
     * @throws YoutubeMediaIsNotAvailableException
     * @throws MediaIsTooOldException
     * @throws DownloadMediaTagException
     */
    public function check():bool
    {
        Log::notice("Getting informations for media {$this->media->media_id}");
        $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

        /** is video downladable (not upcoming and processed) */
        if (!$youtubeVideo->isAvailable()) {
            $message = "This video {$this->media->media_id} is not available yet. 'upcoming' live or not yet 'processed'.";
            Log::notice($message);
            throw new YoutubeMediaIsNotAvailableException($message);
        }

        /** check if media is not too old */
        if (!$this->media->channel->isDateAccepted($this->media->published_at)) {
            $message = "This video {$this->media->media_id} is too old to be downloaded.";
            Log::notice($message);
            throw new MediaIsTooOldException($message);
        }

        /** if media has a tag, is it downladable */
        if (!$this->media->channel->areTagsAccepted($youtubeVideo->tags())) {
            $message = 'Media tags ' . implode(',', $youtubeVideo->tags()) .
                    " are not in allowed tags {$this->media->channel->accept_video_by_tag}.";
            Log::notice($message);
            throw new DownloadMediaTagException($message);
        }

        return true;
    }
}
