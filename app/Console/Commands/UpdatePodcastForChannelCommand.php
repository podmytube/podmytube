<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Factories\UploadPodcastFactory;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

class UpdatePodcastForChannelCommand extends Command
{
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

    /** @var \App\Channel */
    protected $channel;

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

        $this->channel = Channel::findOrFail($this->argument('channelId'));
        $this->info("Updating podcast for channel {$this->channel->nameWithId()}", 'v');

        UploadPodcastFactory::init()->for($this->channel);

        $this->comment("Podcast {$this->channel->nameWithId()} has been successfully updated.", 'v');
        $this->info("You can check it here : {$this->channel->podcastUrl()}", 'v');

        return 0;
    }
}
