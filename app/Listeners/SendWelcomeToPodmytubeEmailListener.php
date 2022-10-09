<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Jobs\SendWelcomeToPodmytubeEmailJob;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeToPodmytubeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(Verified $event): void
    {
        SendWelcomeToPodmytubeEmailJob::dispatch($event->user);
    }
}
