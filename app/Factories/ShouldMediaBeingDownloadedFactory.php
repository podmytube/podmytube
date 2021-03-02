<?php

namespace App\Factories;

use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaIsTooOldException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Media;
use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\Log;

class ShouldMediaBeingDownloadedFactory
{
    /** \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $media */
    protected $media;

    private function __construct(Media $media)
    {
        $this->media = $media;
        $this->channel = $media->channel;
    }

    public static function create(Media $media)
    {
        return new static($media);
    }

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
        if (!$this->channel->isDateAccepted($this->media->published_at)) {
            $message = "This video {$this->media->media_id} is too old to be downloaded.";
            Log::notice($message);
            throw new MediaIsTooOldException($message);
        }

        /** if media has a tag, is it downladable */
        if (!$this->channel->areTagsAccepted($youtubeVideo->tags())) {
            $message = 'Media tags ' . implode(',', $youtubeVideo->tags()) .
                    " are not in allowed tags {$this->media->channel->accept_video_by_tag}.";
            Log::notice($message);
            throw new DownloadMediaTagException($message);
        }

        return true;
    }
}
