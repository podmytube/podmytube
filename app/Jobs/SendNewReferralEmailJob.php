<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\YouHaveNewReferralMail;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewReferralEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $referrer;

    public function __construct(public Channel $channel)
    {
        $this->referrer = $this->channel->user->referrer;
    }

    public function handle(): void
    {
        if ($this->referrer->hasVerifiedEmail()) {
            Mail::to($this->referrer)->send(new YouHaveNewReferralMail($this->channel));
        }
    }
}
