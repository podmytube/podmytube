<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Mail\WelcomeToPodmytubeMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeToPodmytubeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(Verified $event): void
    {
        if ($event->user instanceof User && $event->user->hasVerifiedEmail()) {
            Mail::to($event->user)->send(new WelcomeToPodmytubeMail($event->user));
        }
    }
}
