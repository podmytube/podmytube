<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Factories\UploadPodcastFactory;
use App\Interfaces\InteractsWithPodcastable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UploadPodcast implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithPodcastable $event)
    {
        Log::debug(self::class.'::'.__FUNCTION__.' - start');
        UploadPodcastFactory::init()->for($event->podcastable());

        return true; // only for tests
    }
}
