<?php

namespace App\Listeners;

use App\Events\MediaAdded;
use App\Jobs\UploadMediaJob;

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
     * @param  object  $event
     *
     * @return void
     */
    public function handle(MediaAdded $event)
    {
        info('Listener -- ' . __CLASS__ . '::' . __FUNCTION__);
        UploadMediaJob::dispatchNow($event->media);
    }
}
