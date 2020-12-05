<?php

namespace App\Listeners;

use App\Events\ChannelRegistered;
use App\Jobs\MailChannelIsRegistered;

class SendChannelIsRegisteredEmail
{
    /**
     * Handle the event.
     *
     * @param \App\Events\ChannelRegistered $event
     *
     * @return void
     */
    public function handle(ChannelRegistered $event): void
    {
        /** Sending the channel registered mail within the queue */
        MailChannelIsRegistered::dispatchNow($event->channel);
    }
}
