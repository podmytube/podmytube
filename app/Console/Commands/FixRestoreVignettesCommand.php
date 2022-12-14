<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CreateVignetteFromThumbJob;
use App\Jobs\TransferFileJob;
use App\Models\Channel;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class FixRestoreVignettesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:restore-vignettes {channel_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore vignette';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isDisplay()) {
            $this->warn('This command should run on display only.');

            return 0;
        }

        if ($this->argument('channel_id')) {
            $channels = Channel::with('cover')->where('channel_id', '=', $this->argument('channel_id'))->get();
        } else {
            $channels = Channel::with('cover')->active()->get();
        }

        $channels->each(function (Channel $channel): void {
            $this->info('Restoring vignette for channel ' . $channel->channel_id, 'v');

            if ($channel->hasVignette()) {
                // channel has vignette file => nothing to do
                Log::info("Channel {$channel->youtube_id} already has vignette. Nothing to do.");

                return;
            }

            if (!$channel->hasCover()) {
                // channel has no cover => nothing to do
                Log::info("Channel {$channel->youtube_id} has no cover/thumb. Can do nothing.");

                return;
            }

            $jobToChains = [];
            if (!$channel->coverFileExists()) {
                // thumb not present on display server
                $jobToChains[] = (new TransferFileJob(
                    sourceDisk: 'remote',
                    sourceFilePath: $channel->coverFullPath(),
                    destinationDisk: 'thumbs',
                    destinationFilePath: $channel->coverRelativePath(),
                ))->onQueue('podwww');
            }

            // channel has cover but no vignette file => dispatch job
            $jobToChains[] = new CreateVignetteFromThumbJob($channel->cover);

            Bus::chain($jobToChains)->dispatch();
        });

        return Command::SUCCESS;
    }
}
