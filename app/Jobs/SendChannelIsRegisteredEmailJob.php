<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Interfaces\Podcastable;
use App\Mail\ChannelIsRegisteredMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendChannelIsRegisteredEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Podcastable $podcastable)
    {
    }

    public function handle(): void
    {
        Mail::to($this->podcastable->user)->send(new ChannelIsRegisteredMail($this->podcastable));
    }
}
