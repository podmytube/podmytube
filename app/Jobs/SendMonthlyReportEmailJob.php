<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Interfaces\Podcastable;
use App\Mail\MonthlyReportMail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReportEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Podcastable $podcastable, public Carbon $wantedMonth)
    {
    }

    public function handle(): void
    {
        // Sending monthly report to channel
        Mail::to($this->podcastable->user)->send(new MonthlyReportMail($this->podcastable, $this->wantedMonth));
    }
}
