<?php

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\SendMonthlyReportJob;
use App\Mail\MonthlyReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReports extends Command
{
    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'email:monthlyReport';

    /** @var string $description The console command description. */
    protected $description = 'This command is sending monthly report to every registered channel.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * get channels list
         */
        $channels = Channel::allActiveChannels();

        /**
         * dispatch
         */
        $channels->map(function ($channel) {
            Mail::to($channel->user)->queue(new MonthlyReportMail($channel));
        });

        $this->comment(
            "{$channels->count()} monthly reports emails were successfully queued.",
            'v'
        );
    }
}
