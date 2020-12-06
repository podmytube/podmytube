<?php

namespace App\Listeners;

use App\Events\MediaUploadedByUser;
use App\Jobs\UploadMediaJob;
use Illuminate\Support\Facades\Log;

class UploadMedia
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(MediaUploadedByUser $event)
    {
        /**
         * when a media is added we shoulkd upload it
         */
        Log::debug(
            'Media has been uploaded by user',
            [
                'media_id', $event->media->media_id,
                'channel_id', $event->media->channel->id(),
            ]
        );
        UploadMediaJob::dispatchNow($event->media);
    }
}
