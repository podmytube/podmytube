<?php

namespace App\Listeners;

use App\Events\MediaRegistered;
use App\Jobs\MailChannelHasReachedItsLimit;

class QuotaReachedWarning
{
    /**
     * handle MediaRegistered Event.
     * will create a send an email job to warn user this media will not be generated.
     *
     * @param \App\Events\MediaRegistered $event
     */
    public function handle(MediaRegistered $event)
    {
        $channel = $event->getMedia()->channel->first();
        if ($channel->hasReachedItslimit()) {
            MailChannelHasReachedItsLimit::dispatch($event->getMedia())->delay(
                now()
            );
        }
    }
}
