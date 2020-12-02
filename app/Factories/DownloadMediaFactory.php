<?php

namespace App\Factories;

use App\Exceptions\ChannelHasReachedItsQuotaException;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Media;
use App\Modules\CheckingGrabbedFile;
use App\Modules\DownloadYTMedia;
use App\Modules\MediaProperties;
use App\Youtube\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadMediaFactory
{
    /** @var \App\Media $media */
    protected $media;

    /** @var bool $verbose */
    protected $verbose;

    private function __construct(Media $media)
    {
        $this->media = $media;
    }

    public static function media(...$params)
    {
        return new static(...$params);
    }

    public function run()
    {
        return DB::transaction(function () {
            /**
             * did channel reach its quota
             */
            if ($this->media->channel->hasReachedItslimit()) {
                $message = "Channel {$this->media->channel->channel_name} ({$this->media->channel->channel_id}) has reached its quota.";
                Log::warning($message);
                throw new ChannelHasReachedItsQuotaException($message);
            }

            /**
             * getting media infos
             */
            Log::notice("Getting informations for media {$this->media->media_id}");
            $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

            /**
             * is video downladable (not upcoming and processed)
             */
            if (!$youtubeVideo->isAvailable()) {
                $message = "This video {$this->media->media_id} is not available yet. 'upcoming' live or not yet 'processed'.";
                Log::notice($message);
                throw new YoutubeMediaIsNotAvailableException($message);
            }

            /**
             * if media has a tag, is it downladable
             */
            if ($youtubeVideo->isTagged() && !$this->media->channel->areTagsAccepted($youtubeVideo->tags())) {
                $message = 'Media tags ' . implode(',', $youtubeVideo->tags()) .
                    " are not in allowed tags {$this->media->channel->accept_video_by_tag}.";
                Log::notice($message);
                throw new DownloadMediaTagException($message);
            }

            /** download, convert and get its path */
            Log::notice("Downloading media {$this->media->media_id} from youtube.");
            $downloadedFilePath = DownloadYTMedia::init($this->media->media_id, Storage::disk('tmp')->path(''), false)
                ->download()
                ->downloadedFilePath();

            /**
             * if empty will throw exception
             */
            Log::notice("Media {$this->media->media_id} has been download successfully from youtube. Analyzing.");
            $mediaProperties = MediaProperties::analyzeFile($downloadedFilePath);

            /**
             * checking obtained file duration of result
             */
            CheckingGrabbedFile::init($mediaProperties, $youtubeVideo->duration())
                ->check();

            /**
             * upload it
             */
            Log::notice("Uploading Media from {$downloadedFilePath} to {$this->media->url()} ");
            $this->media->uploadFromPath($downloadedFilePath);

            /**
             * update infos
             */
            Log::notice('Persisting media infos into DB.');
            $this->media->title = $youtubeVideo->title();
            $this->media->description = $youtubeVideo->description();
            $this->media->grabbed_at = Carbon::now();
            $this->media->length = $mediaProperties->filesize();
            $this->media->duration = $mediaProperties->duration();
            $this->media->save();
            Log::notice("Downloading media {$this->media->media_id} is successfully finished.");
            return true;
        });
    }
}
