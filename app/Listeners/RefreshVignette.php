<?php

namespace App\Listeners;

use App\Jobs\CreateVignetteFromThumb;

class RefreshVignette
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        CreateVignetteFromThumb::dispatchNow($event->channel->thumb);
    }
}
