<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithPodcastable;
use App\Mail\ChannelIsRegisteredMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendChannelIsRegisteredEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithPodcastable $event): void
    {
        // Sending the podcastable registered mail within the queue
        Mail::to($event->podcastable->user)->send(
            new ChannelIsRegisteredMail($event->podcastable())
        );
        Log::notice(
            'Newly registered podcastable email has been sent',
            ['channel_id', $event->podcastable->id()]
        );
    }
}
