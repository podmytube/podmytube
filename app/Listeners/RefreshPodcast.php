<?php

namespace App\Listeners;

use App\Events\OccursOnChannel;
use App\Jobs\SendFeedBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefreshPodcast implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     *
     * @return void
     */
    public function handle(OccursOnChannel $event)
    {
        /** rendering feed */
        if (PodcastBuilder::prepare($event->channel)->save()) {
            /** uploading feed */
            SendFeedBySFTP::dispatchNow($event->channel);
        }
    }
}
