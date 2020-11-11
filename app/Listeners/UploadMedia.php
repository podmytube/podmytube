<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Jobs\UploadMediaJob;

class UploadThumb
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
    public function handle(OccursOnChannel $event)
    {
        UploadMediaJob::dispatchNow($event->channel->thumb);
    }
}
