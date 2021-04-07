<?php

namespace App\Factories;

use App\Events\ChannelUpdated;
use App\Exceptions\ChannelHasReachedItsQuotaException;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaIsTooOldException;
use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Modules\CheckingGrabbedFile;
use App\Modules\DownloadYTMedia;
use App\Modules\MediaProperties;
use App\Youtube\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function run(): bool
    {
        try {
            /**
             * check if media is eligible for download
             */
            Log::debug("Should media {$this->media->media_id} being download.");
            ShouldMediaBeingDownloadedFactory::create($this->media)->check();

            /**
             * exhausted quota ?
             */
            if ($this->media->channel->hasReachedItslimit()) {
                $message = "Channel {$this->media->channel->nameWithId()} has reached its quota. Media {$this->media->media_id} won't be downloaded .";
                Log::debug($message);
                throw new ChannelHasReachedItsQuotaException($message);
            }

            /**
             * getting media infos
             */
            Log::debug("Getting informations for media {$this->media->media_id}");
            $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

            /** download, convert and get its path */
            Log::debug("About to download media {$this->media->media_id}.");
            $downloadedFilePath = DownloadYTMedia::init($this->media, '/tmp/', false)
                ->download()
                ->downloadedFilePath();

            /**
             * if empty will throw exception
             */
            Log::debug("Media {$this->media->media_id} has been download successfully from youtube. Analyzing.");
            $mediaProperties = MediaProperties::analyzeFile($downloadedFilePath);

            /**
             * checking obtained file duration of result
             */
            Log::debug("Checking media {$this->media->media_id} duration.");
            CheckingGrabbedFile::init($mediaProperties, $youtubeVideo->duration())->check();

            /**
             * upload it
             */
            Log::debug("Uploading media {$this->media->media_id} duration.");
            SendFileBySFTP::dispatchNow($downloadedFilePath, $this->media->remoteFilePath(), $cleanAfter = true);

            /**
             * setting status
             */
            $status = Media::STATUS_DOWNLOADED;
        } catch (YoutubeMediaDoesNotExistException $exception) {
            $status = Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE;
        } catch (YoutubeMediaIsNotAvailableException $exception) {
            $status = Media::STATUS_NOT_PROCESSED_ON_YOUTUBE;
        } catch (DownloadMediaTagException $exception) {
            $status = Media::STATUS_TAG_FILTERED;
        } catch (MediaIsTooOldException $exception) {
            $status = Media::STATUS_AGE_FILTERED;
        } catch (ChannelHasReachedItsQuotaException $exception) {
            $status = Media::STATUS_EXHAUSTED_QUOTA;
        }

        /**
         * update infos
         */
        Log::notice('Persisting media infos into DB.');

        $updateParams = [
            'grabbed_at' => $status === Media::STATUS_DOWNLOADED ? Carbon::now() : null,
            'status' => $status,
        ];
        if (isset($youtubeVideo)) {
            $updateParams = array_merge($updateParams, ['title' => $youtubeVideo->title(), 'description' => $youtubeVideo->description(), ]);
        }
        if (isset($mediaProperties)) {
            $updateParams = array_merge($updateParams, [
                'length' => $mediaProperties->filesize(),
                'duration' => $mediaProperties->duration(),
            ]);
        }
        $this->media->update($updateParams);

        Log::debug("Processing media {$this->media->media_id} is finished.");
        ChannelUpdated::dispatch($this->media->channel);
        return true;
    }
}
