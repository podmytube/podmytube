<?php

namespace App\Factories;

use App\Channel;
use App\Jobs\SendFileBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadPodcastFactory
{
    /** @var \App\Channel $channel */
    protected $channel;

    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function forChannel(Channel $channel)
    {
        $this->channel = $channel;

        /** getting rendered podcast */
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();

        /** saving it in tmp */
        Storage::disk('tmp')->put($this->localFilename(), $renderedPodcast);

        /** uploading */
        SendFileBySFTP::dispatchNow($this->localPath(), $this->remotePath(), $cleanAfter = true);

        Log::debug("Podcast {$channel->nameWithId()} has been successfully updated.");
        Log::debug("You can check it here : {$channel->podcastUrl()}");
        return $this;
    }

    public function localFilename():string
    {
        return "{$this->channel->channelId()}-" . config('app.feed_filename');
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