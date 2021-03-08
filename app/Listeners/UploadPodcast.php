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
        Log::debug(self::class . '::' . __FUNCTION__ . ' - start');
        UploadPodcastFactory::init()->for($event->podcastable);
        return true; // only for tests
    }
}
