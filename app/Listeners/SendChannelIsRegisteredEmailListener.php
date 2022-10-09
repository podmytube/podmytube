<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Interfaces\InteractsWithPodcastable;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChannelIsRegisteredEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InteractsWithPodcastable $event): void
    {
        SendChannelIsRegisteredEmailJob::dispatch($event->podcastable());
    }
}
