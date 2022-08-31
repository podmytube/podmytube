<?php

declare(strict_types=1);

namespace App\Factories;

use App\Events\ChannelUpdated;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaIsTooOldException;
use App\Exceptions\YoutubeAndLocalDurationException;
use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Jobs\SendFileByRsync;
use App\Models\Media;
use App\Modules\CheckingGrabbedFile;
use App\Modules\DownloadYTMedia;
use App\Modules\MediaProperties;
use App\Youtube\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DownloadMediaFactory
{
    /** @var \App\Models\Media */
    protected $media;

    /** @var bool */
    protected $force;

    /** @var bool */
    protected $verbose;

    private function __construct(Media $media, bool $force = false)
    {
        $this->media = $media;
        $this->force = $force;
    }

    public static function media(Media $media, bool $force = false)
    {
        return new static($media, $force);
    }

    public function run(): bool
    {
        try {
            if ($this->force === false) {
                // if forced we download it whatever its quota/plan situation.
                $this->shouldWeDownloadMedia();
            }

            // getting media infos
            Log::debug("Getting informations for media {$this->media->media_id}");
            $youtubeVideo = YoutubeVideo::forMedia($this->media->media_id);

            // download, convert and get its path
            Log::debug("About to download media {$this->media->media_id}.");
            $downloadedFilePath = DownloadYTMedia::init($this->media, '/tmp/', false)
                ->download()
                ->downloadedFilePath()
            ;

            // if empty will throw exception
            Log::debug("Media {$this->media->media_id} has been download successfully from youtube. Analyzing.");
            $mediaProperties = MediaProperties::analyzeFile($downloadedFilePath);

            // checking obtained file duration of result
            Log::debug("Checking media {$this->media->media_id} duration.");
            CheckingGrabbedFile::init($mediaProperties, $youtubeVideo->duration())->check();

            // upload it
            Log::debug("Uploading media {$this->media->media_id} duration.");
            SendFileByRsync::dispatchSync($downloadedFilePath, $this->media->remoteFilePath(), $cleanAfter = true);

            /**
             * setting status.
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
        } catch (YoutubeAndLocalDurationException $exception) {
            $status = Media::STATUS_NOT_DOWNLOADED;
        }

        // update infos
        Log::notice('Persisting media infos into DB.');

        $updateParams = [
            'grabbed_at' => $status === Media::STATUS_DOWNLOADED ? Carbon::now() : null,
            'status' => $status,
        ];
        if (isset($youtubeVideo)) {
            $updateParams = array_merge(
                $updateParams,
                [
                    'title' => $youtubeVideo->title(),
                    'description' => $youtubeVideo->description(),
                ]
            );
        }
        if (isset($mediaProperties)) {
            $updateParams = array_merge($updateParams, [
                'length' => $mediaProperties->filesize(),
                'duration' => $mediaProperties->duration(),
            ]);
        }
        $this->media->update($updateParams);

        Log::debug("Processing media {$this->media->media_id} is finished.");
        if ($status === Media::STATUS_DOWNLOADED) {
            // media has been dowloaded => update podcast feed
            ChannelUpdated::dispatch($this->media->channel);
        }

        return true;
    }

    public function isForced(): bool
    {
        return $this->force === true;
    }

    protected function shouldWeDownloadMedia(): bool
    {
        // check if media is eligible for download
        Log::debug("Should media {$this->media->media_id} being download.");
        ShouldMediaBeingDownloadedFactory::create($this->media)->check();

        return true;
    }
}
