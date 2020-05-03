<?php

namespace App\Console\Commands;

use App\Channel;
use App\Plan;
use Illuminate\Console\Command;

class ChannelUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:update 
                            {channelTypeToUpdate=all : options are free/paying/all} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update list of episodes by type of channels';

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
        // parse argument
        $typesAllowed = ['free', 'paying', 'early', 'all'];

        if (!in_array($this->argument('channelTypeToUpdate'), $typesAllowed)) {
            $this->error(
                'Only these options are available : ' .
                    implode(', ', $typesAllowed)
            );
            return false;
        }

        // get channel(s) to refresh (free/early/all/..)
        $channels = Channel::byPlanType(
            $this->argument('channelTypeToUpdate')
        )->filter(function ($channel) {
            // quota reached control
            return $channel->hasReachedItslimit() === false;
        });

        dump($channels);
        // get youtube videos for each channel

        // save it as a media in db
    }
}
