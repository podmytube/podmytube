<?php

namespace App\Listeners;

use App\Jobs\MailChannelIsRegistered;
use App\Events\ChannelRegistered;

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
    public function handle(ChannelRegistered $event): void
    {
        /** Sending the channel registered mail within the queue */
        MailChannelIsRegistered::dispatchNow($event->channel);
    }
}
