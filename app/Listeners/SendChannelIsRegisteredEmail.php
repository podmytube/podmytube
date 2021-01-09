<?php

namespace App\Listeners;

use App\Mail\ChannelIsRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendChannelIsRegisteredEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        /** Sending the channel registered mail within the queue */
        Mail::to($event->channel->user)->send(
            new ChannelIsRegistered($event->channel)
        );
        Log::debug(
            'Newly registered channel email has been sent',
            ['channel_id', $event->channel->id(), ]
        );
    }
}
