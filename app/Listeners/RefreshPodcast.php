<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Podcast\PodcastBuilder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshPodcast
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OccursOnChannel $event)
    {
        PodcastBuilder::prepare($event->channel)->save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    /* public function subscribe($events)
    {
        $events->listen(
            'App\Events\ChannelRegistered',
            'App\Listeners\RefreshPodcast@make'
        );
        $events->listen(
            'App\Events\ChannelUpdated',
            'App\Listeners\RefreshPodcast@make'
        );
        $events->listen(
            'App\Events\ThumbUpdated',
            'App\Listeners\RefreshPodcast@make'
        );
    }

    public function make(OccursOnChannel $event)
    {
        PodcastBuilder::prepare($event->channel)->save();
    } */
}
