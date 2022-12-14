<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Jobs\SendNewReferralEmailJob;
use App\Jobs\UploadPodcastJob;
use App\Models\Channel;

class ChannelIsRegisteredListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle(InteractsWithPodcastable $event): void
    {
        // should build and upload podcast
        UploadPodcastJob::dispatch($event->podcastable());

        // should send a welcome email
        SendChannelIsRegisteredEmailJob::dispatch($event->podcastable());

        if (
            $event->podcastable() instanceof Channel
            && $event->podcastable()->user->referrer !== null
        ) {
            SendNewReferralEmailJob::dispatch($event->podcastable());
        }
    }
}
