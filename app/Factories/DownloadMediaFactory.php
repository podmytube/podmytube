<?php

namespace App\Factories;

use App\Media;
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
             * if media has a tag, is it downladable
             */
            if ($youtubeVideo->isTagged() && !$this->media->channel->areTagsAccepted($youtubeVideo->tags())) {
                return false;
            }

            /**
             * did channel reach its quota
             */

            /**
             * download and convert it
             */
            DownloadYTMedia::init($this->media->media_id, '/tmp', $this->verbose)->download();

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
