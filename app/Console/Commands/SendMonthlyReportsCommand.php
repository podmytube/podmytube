<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Jobs\SendMonthlyReportEmailJob;
use App\Models\Channel;
use App\Modules\ServerRole;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMonthlyReportsCommand extends Command
{
    use BaseCommand;

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

        $this->prologue();

        /** @var Carbon $wantedMonth */
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
        $channels->each(function ($channel) use ($wantedMonth): void {
            if ($channel->user->newsletter) {
                SendMonthlyReportEmailJob::dispatch($channel, $wantedMonth);
            }
        });

        $this->comment(
            "{$channels->count()} monthly reports emails were successfully queued.",
            'v'
        );

        $this->epilogue();

        return 0;
    }
}
