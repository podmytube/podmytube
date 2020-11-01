<?php

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\SendFeedBySFTP;
use App\Podcast\PodcastBuilder;
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * getting channel to build podcast for
         */
        $channel = Channel::findOrFail($this->argument('channelId'));
        $this->info("Updating podcast for channel {$channel->channel_name} ({$channel->channel_id})", 'v');

        if (PodcastBuilder::prepare($channel)->save()) {
            /** uploading feed */
            SendFeedBySFTP::dispatchNow($channel);
        }

        $this->comment("Podcast {{$channel->title()}} has been successfully created.", 'v');
        $this->info("You can check it here : {$channel->podcastUrl()}", 'v');
    }
}
