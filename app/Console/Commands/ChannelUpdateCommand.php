<?php

namespace App\Console\Commands;

use App\ApiKey;
use App\Channel;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeCore;
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

    /** @var \App\ApiKey $apikey youtube apikey to use */
    protected $apikey;

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

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
        /** =============================================
         * NOT IN THE __CONSTRUCT
         * construct is read with artisan command. If you try to access a table
         * before migration happen even artisan list is failing.
         */
        $this->apikey = ApiKey::make()->get();
        $this->youtubeCore = YoutubeCore::init($this->apikey);
        // =============================================

        // parse argument
        $typesAllowed = ['free', 'paying', 'early', 'all'];

        if (!in_array($this->argument('channelTypeToUpdate'), $typesAllowed)) {
            $this->error(
                'Only these options are available : ' .
                    implode(', ', $typesAllowed)
            );
            return;
        }

        // get channel(s) to refresh (free/early/all/..)
        $channels = Channel::byPlanType(
            $this->argument('channelTypeToUpdate')
        )->filter(function ($channel) {
            // quota reached control
            return $channel->hasReachedItslimit() === false;
        });

        if (!$channels->count()) {
            $this->error(
                "There is no channels with this kind of plan ({$this->argument(
                    'channelTypeToUpdate'
                )})"
            );
            return;
        }

        /** each channel */
        $channels->map(function ($channel) {
            /** get videos */
            //YoutubeChannel::init($this->youtubeCore)->forChannel($channel->channel_id)->
            /** save it as a media in db */
        });
    }
}
