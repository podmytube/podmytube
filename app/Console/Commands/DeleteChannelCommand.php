<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Jobs\ChannelCleaningJob;
use App\Models\Channel;
use Illuminate\Console\Command;

class DeleteChannelCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:channel {channel_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete channel and all data about it.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();
        $channel = Channel::byChannelId($this->argument('channel_id'));
        if (!$channel) {
            $this->error("There is no registered channel with this id ({$this->argument('channel_id')}).");

            return 1;
        }

        ChannelCleaningJob::dispatch($channel);

        $this->line("Channel {$this->argument('channel_id')} is queued to be deleted soon.");

        $this->epilogue();

        return 0;
    }
}
