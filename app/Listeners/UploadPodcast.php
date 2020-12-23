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
        $context = [
            'channel_name' => $event->channel->title(),
            'channel_id' => $event->channel->id(),
            'remote_host' => config('filesystems.disks.sftpfeeds.host'),
            'feed url' => $event->channel->podcastUrl()
        ];
        Log::debug(
            'Uploading podcast for channel ',
            $context
        );
        try {
            PodcastUpload::prepare($event->channel)->upload();
            Log::debug('Podcast was uploaded successfully.', $context);
        } catch (Exception $exception) {
            Log::error(
                "Uploading podcast for channel has failed with {$exception->getMessage()}",
                $context
            );
        }
    }
}
