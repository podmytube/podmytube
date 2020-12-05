<?php

namespace App\Listeners;

use App\Events\MediaAdded;
use App\Jobs\UploadMediaJob;
use Illuminate\Support\Facades\Log;

class MediaIsAdded
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
    public function handle(MediaAdded $event)
    {
        /**
         * when a media is added we shoulkd upload it
         */
        Log::notice("One media has been added ({$event->media->media_id}).");
        UploadMediaJob::dispatchNow($event->media);
    }
}
