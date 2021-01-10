<?php

namespace App\Factories;

use App\Jobs\SendFileBySFTP;
use App\Modules\Vignette;
use App\Thumb;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RefreshVignetteFactory
{
    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Modules\Vignette $vignette */
    protected $vignette;

    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function forThumb(Thumb $thumb)
    {
        $this->channel = $thumb->channel;

        /** getting rendered podcast */
        $this->vignette = Vignette::fromThumb($thumb)->makeIt();

        /** saving it locally */
        // Storage::disk('tmp')->put($this->localFilename(), $renderedPodcast);

        /** uploading */
        /* SendFileBySFTP::dispatchNow($this->localPath(), $this->remotePath(), $cleanAfter = true);

        Log::debug("Podcast {$channel->nameWithId()} has been successfully updated.");
        Log::debug("You can check it here : {$channel->podcastUrl()}"); */
        return $this;
    }

    public function localFilename():string
    {
        return $this->vignette->localFilePath();
    }

    public function localPath():string
    {
        return Storage::disk('tmp')->path($this->localFilename());
    }

    public function remotePath():string
    {
        return $this->channel->remoteFilePath();
    }
}
