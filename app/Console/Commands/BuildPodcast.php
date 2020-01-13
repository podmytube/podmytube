<?php

namespace App\Console\Commands;

use App\Channel;
use App\Podcast\PodcastBuilder;
use App\Podcast\PodcastHeader;
use Illuminate\Console\Command;


class BuildPodcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:build {channelId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will build one podcast feed from its channel/medias infos.';

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
        /**
         * getting channel to build podcast for
         */
        $channel = Channel::findOrFail($this->argument('channelId'));

        /**
         * creating podcast
         */
        ($podcastObj = PodcastBuilder::prepare($channel))->save();
        $this->info("Podcast {{$channel->title()}} has been successfully created.");
        $this->info("You can check it here : ".$podcastObj->url());
    }
}
