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
        /** Sending the podcastable registered mail within the queue */
        Mail::to($event->podcastable->user)->send(
            new ChannelIsRegistered($event->podcastable)
        );
        Log::debug(
            'Newly registered podcastable email has been sent',
            ['channel_id', $event->podcastable->id()]
        );
    }
}
