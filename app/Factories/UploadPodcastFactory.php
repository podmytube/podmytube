<?php

namespace App\Factories;

use App\Channel;
use App\Jobs\SendFileBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadPodcastFactory
{
    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function forChannel(Channel $channel)
    {
        /** getting rendered podcast */
        $renderedPodcast = PodcastBuilder::create($channel->toPodcast())->render();

        /** saving it in tmp */
        $fileName = "{$channel->channelId()}." . config('app.feed_filename');
        Storage::disk('tmp')->put($fileName, $renderedPodcast);

        /** uploading */
        $localPath = Storage::disk('tmp')->path($fileName);
        $remotePath = $channel->remoteFilePath();
        SendFileBySFTP::dispatchNow($localPath, $remotePath, $cleanAfter = true);

        Log::debug("Podcast {$channel->nameWithId()} has been successfully updated.");
        Log::debug("You can check it here : {$channel->podcastUrl()}");
    }
}
