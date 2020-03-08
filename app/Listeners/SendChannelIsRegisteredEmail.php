<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Jobs\MailChannelIsRegistered;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendChannelIsRegisteredEmail
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ChannelRegistered  $event
     * @return void
     */
    public function handle(OccursOnChannel $event): void
    {
        /** Sending the channel registered mail within the queue */
        MailChannelIsRegistered::dispatchNow($event->channel);
    }
}
