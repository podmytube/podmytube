<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ChannelHasReachedItsLimitsMail;
use App\Models\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ChannelHasReachedItsLimitsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Channel $channel)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->channel->user->wantToBeWarnedForExceedingQuota()) {
            Mail::to($this->channel->user->email)->queue(new ChannelHasReachedItsLimitsMail($this->channel));
        }
    }
}
