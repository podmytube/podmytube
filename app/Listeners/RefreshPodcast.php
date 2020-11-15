<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Jobs\SendFeedBySFTP;
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
    public function handle(OccursOnChannel $event)
    {
        Log::notice(__CLASS__ . '::' . __FUNCTION__);
        Log::notice("{$event->channel->channel_name} podcast is about to be refreshed.");
        /** rendering feed */
        if (PodcastBuilder::prepare($event->channel)->save()) {
            /** uploading feed */
            SendFeedBySFTP::dispatchNow($event->channel);
        }
        Log::notice("{$event->channel->channel_name} podcast has been refreshed.");
    }
}
