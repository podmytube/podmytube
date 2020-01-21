<?php

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\SendFeedBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Console\Command;

class BatchPodcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:batch 
        {--all : will generate all active podcasts } 
        {--free : will generate only the free ones } 
        {--early : will generate only the early birds } 
        {--paying : will generate only paying channels }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will podcast feeds by kind.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $channels = Channel::allActiveChannels()->filter(function ($channel){
            var_dump(get_class($channel));
        });
        //var_dump(DB::connection()->getQueryLog());
        /*
        // getting channel to build podcast for
        $channel = Channel::findOrFail($this->argument('channelId'));

        if (PodcastBuilder::prepare($channel)->save()) {
            // uploading feed
            SendFeedBySFTP::dispatchNow($channel);
        }
        //event(new ChannelUpdated($channel));

        $this->info("Podcast {{$channel->title()}} has been successfully created.");
        $this->info("You can check it here : " . $channel->podcastUrl());
        */
    }
}
