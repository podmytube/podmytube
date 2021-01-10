<?php

namespace App\Listeners;

use App\Factories\UploadPodcastFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UploadPodcast implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        Log::debug('--- ' . __CLASS__ . ' start');
        UploadPodcastFactory::init()->forChannel($event->channel);
        Log::debug('--- ' . __CLASS__ . ' end');
    }
}
