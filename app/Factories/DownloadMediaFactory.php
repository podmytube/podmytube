<?php

namespace App\Factories;

use App\Exceptions\ChannelHasReachedItsQuotaException;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Media;
use App\Modules\CheckAudioDuration;
use App\Modules\DownloadYTMedia;
use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\DB;

class DownloadMediaFactory
{
    private function __construct(Media $media, bool $verbose = false)
    {
        $this->media = $media;
        $this->verbose = $verbose;
    }

    public static function media(...$params)
    {
        return new static(...$params);
    }

    public function run()
    {
        return DB::transaction(function () {
            /**
             * getting media infos
             */
            $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

            /**
             * is video downladable (not upcoming and processed)
             */
            if (!$youtubeVideo->isAvailable()) {
                throw new YoutubeMediaIsNotAvailableException(
                    "This video {$this->media->media_id} is not available yet. \
                    'upcoming' live or not yet 'processed'."
                );
            }

            /**
             * if media has a tag, is it downladable
             */
            if ($youtubeVideo->isTagged() && !$this->media->channel->areTagsAccepted($youtubeVideo->tags())) {
                throw new DownloadMediaTagException(
                    'Media tags ' . implode(',', $youtubeVideo->tags()) .
                    " are not in allowed tags {$this->media->channel->accept_video_by_tag}."
                );
            }

            /**
             * did channel reach its quota
             */
            if ($this->media->channel->hasReachedItslimit()) {
                throw new ChannelHasReachedItsQuotaException(
                    "Channel $this->media->channel->channel_name ($this->media->channel->channel_id) \
                    has reached its quota."
                );
                return false;
            }

            /**
             * download and convert it
             */
            $downloadedFilePath = DownloadYTMedia::init($this->media->media_id, '/tmp', $this->verbose)->download()->downloadedFilePath();

            /**
             * check duration of result
             */
            CheckAudioDuration::init($youtubeVideo->duration(), $downloadedFilePath)->check();

            /**
             * upload it
             */
            

            /**
             * update infos
             */

            return true;
        });
    }
}
