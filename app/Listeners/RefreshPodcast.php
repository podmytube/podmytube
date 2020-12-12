<?php

namespace App\Listeners;

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
        Log::debug(
            'Refreshing podcast for channel',
            ['channel_id' => $event->channel->id(), ]
        );

        $result = PodcastBuilder::forChannel($event->channel)->build()->save();
        if ($result === false) {
            $message = "Updating podcast for channel {$event->channel->title()} ({$event->channel->id()}) has failed.";
            Log::error($message);
            throw new PodcastUpdateFailureException($message);
        }
        Log::debug(
            'Podcast has been generated for channel',
            [
                'channel_id' => $event->channel->id(),
                'podcast url' => $event->channel->podcastUrl(),
            ]
        );
    }
}
