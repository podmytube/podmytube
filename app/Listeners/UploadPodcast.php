<?php

namespace App\Listeners;

use App\Factories\UploadPodcastFactory;
use Illuminate\Queue\InteractsWithQueue;

class UploadPodcast
{
    use InteractsWithQueue;

    public function handle($event)
    {
        UploadPodcastFactory::init()->forChannel($event->channel);
    }
}
