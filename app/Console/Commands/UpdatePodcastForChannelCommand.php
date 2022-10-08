<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Factories\UploadPodcastFactory;
use App\Models\Channel;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

class UpdatePodcastForChannelCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:podcast {channelId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will build one podcast feed at a time.';

    protected ?Channel $channel;

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
        $this->channel = Channel::findOrFail($this->argument('channelId'));
        $this->info("Updating podcast for channel {$this->channel->nameWithId()}", 'v');

        UploadPodcastFactory::for($this->channel)->run();

        $this->comment("Podcast {$this->channel->nameWithId()} has been successfully updated.", 'v');
        $this->info("You can check it here : {$this->channel->podcastUrl()}", 'v');

        $this->epilogue();

        return Command::SUCCESS;
    }
}
