<?php

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\SendFeedBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Console\Command;
use RuntimeException;

class BatchPodcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:batch {batchToProcess=all : options are all/free/paying/early}';

    /* {--all : will generate all active podcasts } 
        {--free : will generate only the free ones } 
        {--early : will generate only the early birds } 
        {--paying : will generate only paying channels }'; */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will build podcast feeds by kind.';


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
        $channels = null;
        switch ($optionTyped = $this->argument('batchToProcess')) {
            case 'free':
                $channels = Channel::freeChannels();
                break;
            case 'paying':
                $channels = Channel::payingChannels();
                break;
            case 'early':
                $channels = Channel::earlyBirdsChannels();
                break;
            case 'all':
                $channels = Channel::allActiveChannels();
                break;
            default:
                throw new RuntimeException("Option $optionTyped is not a valid one. Options available : free/paying/early/all.");
        }

        $bar = $this->output->createProgressBar(count($channels));
        $bar->start();

        $finalMessage = PHP_EOL;
        foreach ($channels as $channel) {
            if (($podcastBuilderObj = PodcastBuilder::prepare($channel))->save()) {
                // uploading feed
                SendFeedBySFTP::dispatchNow($channel);
                /* $this->info("Podcast {{$channel->title()}} has been successfully created.");
                $this->info("You can check it here : " . $podcastBuilderObj->path()); */
                $finalMessage .= "Channel {$channel->title()} {{$channel->channelId()}} has been generated " . PHP_EOL .
                    " {{$podcastBuilderObj->path()}} - {{$channel->podcastUrl()}} " . PHP_EOL;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info($finalMessage);
    }
}
