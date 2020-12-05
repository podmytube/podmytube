<?php

namespace App\Listeners;

use App\Events\PodcastUpdated;
use App\Podcast\PodcastUpload;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UploadPodcast
{
    use InteractsWithQueue;

    public function handle(PodcastUpdated $event)
    {
        Log::notice("Uploading podcast for channel {$event->channel->nameWithId()}.");
        try {
            PodcastUpload::prepare($event->channel)->upload();
            Log::notice('Podcast was uploaded successfully.');
        } catch (Exception $exception) {
            Log::error("Uploading podcast for channel {$event->channel->nameWithId()} has failed with" . $exception->getMessage() . '.');
        }
    }
}
