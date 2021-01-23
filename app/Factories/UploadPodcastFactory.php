<?php

namespace App\Factories;

use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        /** saving it in tmp */
        Storage::disk('tmp')->put($this->podcastable->relativeFeedPath(), $renderedPodcast);

        /** uploading */
        SendFileBySFTP::dispatchNow($this->localPath(), $this->remotePath(), $cleanAfter = true);

        Log::debug("Podcast {$podcastable->podcastTitle()} has been successfully updated.");
        Log::debug("You can check it here : {$podcastable->podcastUrl()}");
        return $this;
    }

    public function localPath():string
    {
        return Storage::disk('tmp')->path($this->podcastable->relativeFeedPath());
    }

    public function remotePath():string
    {
        return $this->podcastable->remoteFilePath();
    }
}
