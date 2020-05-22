<?php

namespace App\Console\Commands;

use App\Channel;
use App\Events\MediaRegistered;
use App\Media;
use App\Youtube\YoutubeChannel;
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
        $channels = Channel::byPlanType($this->argument('channelTypeToUpdate'));

        if (!$channels->count()) {
            $this->error(
                "There is no channels with this kind of plan ({$this->argument(
                    'channelTypeToUpdate'
                )})"
            );
            return;
        }

        $this->info('Updating channels.', 'v');

        if ($this->getOutput()->isVerbose()) {
            $this->bar = $this->output->createProgressBar($channels->count());
            $this->bar->start();
        }

        /** for each channel */
        $channels->map(function ($channel) {
            /** for each channel video */
            array_map(function ($video) {
                $newMedia = false;
                /** check if the video already exist in database */
                if (!($media = Media::find($video['media_id']))) {
                    $media = new Media();
                    $media->media_id = $video['media_id'];
                    $media->channel_id = $video['channel_id'];
                    $newMedia = true;
                }
                // update it
                $media->title = $video['title'];
                $media->description = $video['description'];
                $media->published_at = $video['published_at'];

                /** save it */
                $media->save();

                if ($newMedia) {
                    dump('New media', __FILE__ . '-' . __FUNCTION__);
                    event(new MediaRegistered($media));
                }
            }, YoutubeChannel::forChannel($channel->channel_id)->videos());
            if ($this->getOutput()->isVerbose()) {
                $this->bar->advance();
            }
        });

        if ($this->getOutput()->isVerbose()) {
            $this->bar->finish();
        }
    }
}
