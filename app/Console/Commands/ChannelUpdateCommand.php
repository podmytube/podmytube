<?php

namespace App\Console\Commands;

use App\ApiKey;
use App\Channel;
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

    protected $apikey;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->apikey = ApiKey::make()->get();
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

        /** each channel */
        $channels->map(function ($channel) {
            dump($channel, __FILE__ . '-' . __FUNCTION__);
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParams(['id' => $channel->id])
                ->addParts([
                    'id',
                    'snippet',
                    'invalidPart',
                    'contentDetails',
                    'player',
                    'contentOwnerDetails',
                ])
                ->url();
        });

        if (!$channels->count()) {
            $this->error(
                "There is no channels with this kind of plan ({$this->argument(
                    'channelTypeToUpdate'
                )})"
            );
            return;
        }

        // get youtube videos for each channel
        foreach ($channels as $channel) {
            dump(
                "{$channel->channel_name} ($channel->channel_id)",
                __FILE__ . '-' . __FUNCTION__
            );
        }

        // save it as a media in db
    }
}
