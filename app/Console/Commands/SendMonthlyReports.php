<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Mail\MonthlyReportMail;
use App\Modules\ServerRole;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReports extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'email:monthlyReport {--period=}';

    /** @var string The console command description. */
    protected $description = 'This command is sending monthly report to every registered channel.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $wantedMonth = $this->option('period') !== null ?
            Carbon::createFromFormat('Y-m', $this->option('period'))->startOfMonth() :
            Carbon::now()->startOfMonth()->subMonth();

        /**
         * get channels list.
         */
        $channels = Channel::allActiveChannels();

        if (!$channels->count()) {
            $message = 'There is no channels to send report for.';
            $this->error($message);
            Log::error($message);

            return 1;
        }

        // dispatch
        $channels->map(function ($channel) use ($wantedMonth): void {
            if ($channel->user->newsletter) {
                Mail::to($channel->user)->queue(new MonthlyReportMail($channel, $wantedMonth));
            }
        });

        $this->comment(
            "{$channels->count()} monthly reports emails were successfully queued.",
            'v'
        );

        return 0;
    }
}
