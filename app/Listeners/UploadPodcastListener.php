<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Factories\UploadPodcastFactory;
use App\Interfaces\InteractsWithPodcastable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UploadPodcastListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithPodcastable $event)
    {
        Log::debug('About to upload podcast feed for ' . $event->podcastable()->nameWithId());
        UploadPodcastFactory::for($event->podcastable())->run();

        return true; // only for tests
    }
}
