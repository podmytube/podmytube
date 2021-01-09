<?php

namespace App\Console\Commands;

use App\Channel;
use App\Factories\UploadPodcastFactory;
use Illuminate\Console\Command;

class UpdatePodcastCommand extends Command
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

    /** @var \App\Channel $channel */
    protected $channel;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->channel = Channel::findOrFail($this->argument('channelId'));
        $this->info("Updating podcast for channel {$this->channel->nameWithId()}", 'v');

        UploadPodcastFactory::init()->forChannel($this->channel);

        $this->comment("Podcast {$this->channel->nameWithId()} has been successfully updated.", 'v');
        $this->info("You can check it here : {$this->channel->podcastUrl()}", 'v');
    }
}
