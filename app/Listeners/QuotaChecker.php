<?php

namespace App\Listeners;

use App\Events\MediaRegistered;
use App\Jobs\MailChannelHasReachedItsLimit;

class QuotaChecker
{
    /** @var \App\Channel $channel */
    protected $channel;

    /**
     * handle MediaRegistered Event.
     * will create a send an email job to warn user this media will not be generated.
     *
     * @param \App\Events\MediaRegistered $event
     */
    public function handle(MediaRegistered $event)
    {
        $this->channel = $event->getMedia()->channel;
        if ($this->channel->hasReachedItslimit()) {
            info(
                "Channel {$this->channel->channel_name} has reached its limits."
            );
            MailChannelHasReachedItsLimit::dispatch($event->getMedia())->delay(
                now()->addSeconds(20)
            );
        }
    }
}
