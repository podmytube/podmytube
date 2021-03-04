<?php

namespace App\Factories;

use App\Events\ChannelUpdated;
use App\Jobs\SendFileBySFTP;
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
             * getting media infos
             */
            Log::notice("Getting informations for media {$this->media->media_id}");
            $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

            /**
             * check if media is eligible for download
             */
            ShouldMediaBeingDownloadedFactory::create($this->media)->check();

            /** download, convert and get its path */
            $downloadedFilePath = DownloadYTMedia::init($this->media, Storage::disk('tmp')->path(''), false)
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
            CheckingGrabbedFile::init($mediaProperties, $youtubeVideo->duration())->check();

            /**
             * upload it
             */
            SendFileBySFTP::dispatchNow($downloadedFilePath, $this->media->remoteFilePath(), $cleanAfter = true);

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

            ChannelUpdated::dispatch($this->media->channel);
            return true;
        });
    }
}
