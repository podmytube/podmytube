<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Jobs\CreateVignetteFromThumb;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshVignette
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
     * @return void
     */
    public function handle(OccursOnChannel $event)
    {
        CreateVignetteFromThumb::dispatchNow($event->channel->thumb);
    }
}
