<?php

namespace App\Listeners;

use App\Events\PodcastUpdated;
use App\Exceptions\PodcastUpdateFailureException;
use App\Podcast\PodcastBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RefreshPodcast implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        Log::debug("Refreshing podcast for channel {$event->channel->nameWithId()}");
        $factory = PodcastBuilder::forChannel($event->channel);
        $result = $factory->build()->save();
        if ($result === false) {
            $message = "Updating podcast for channel {$event->channel->nameWithId()}) has failed.";
            Log::error($message);
            throw new PodcastUpdateFailureException($message);
        }
        Log::debug("Podcast has been generated {$factory->url()}");
        PodcastUpdated::dispatch($event->channel);
    }
}
