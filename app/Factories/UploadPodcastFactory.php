<?php

namespace App\Factories;

use App\Exceptions\PodcastSavingFailureException;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;

class UploadPodcastFactory
{
    /** @var \App\Interfaces\Podcastable $podcastable */
    protected $podcastable;

    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function for(Podcastable $podcastable)
    {
        $this->podcastable = $podcastable;

        /** getting rendered podcast */
        $renderedPodcast = PodcastBuilder::create($this->podcastable->toPodcast())->render();

        /** saving it in /tmp */
        $localPath = $this->localPath();

        $this->checkLocalPath();

        $status = file_put_contents($localPath, $renderedPodcast);
        if ($status === false) {
            throw new PodcastSavingFailureException("Saving rendered podcast to {$localPath} has failed.");
        }

        /** uploading */
        SendFileBySFTP::dispatchSync($localPath, $this->remotePath(), $cleanAfter = true);

        Log::debug("Podcast {$podcastable->podcastTitle()} has been successfully updated.");
        Log::debug("You can check it here : {$podcastable->podcastUrl()}");
        return $this;
    }

    public function localPath()
    {
        return "/tmp/{$this->podcastable->relativeFeedPath()}";
    }

    public function checkLocalPath()
    {
        $dirname = pathinfo($this->localPath(), PATHINFO_DIRNAME);
        if (!is_dir($dirname)) {
            if (!mkdir($dirname)) {
                throw new PodcastSavingFailureException("mkdir {$dirname} has failed, cannot save it locally.");
            }
        }
        return true;
    }

    public function remotePath(): string
    {
        return $this->podcastable->remoteFilePath();
    }
}
