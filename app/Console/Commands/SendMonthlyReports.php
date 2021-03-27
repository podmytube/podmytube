<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\MonthlyReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReports extends Command
{
    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'email:monthlyReport {--period=}';

    /** @var string $description The console command description. */
    protected $description = 'This command is sending monthly report to every registered channel.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wantedMonth = $this->option('period') !== null ? Carbon::createFromFormat('Y-m', $this->option('period'))->startOfMonth() : Carbon::now()->startOfMonth()->subMonth();

        /**
         * get channels list
         */
        $channels = Channel::allActiveChannels();

        /**
         * dispatch
         */
        $channels->map(function ($channel) use ($wantedMonth) {
            Mail::to($channel->user)->queue(new MonthlyReportMail($channel, $wantedMonth));
        });

        $this->comment(
            "{$channels->count()} monthly reports emails were successfully queued.",
            'v'
        );
    }
}
