<?php

namespace App\Console\Commands;

use App\Channel;
use App\Media;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;

class UpdateChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:channel {channel_id} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update list of episodes for specific channel';

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

    /** @var App\Channel[] $channels list of channel models */
    protected $channels = [];

    /** @var string[] $errors list of errors that occured */
    protected $errors = [];

    /** @var \Symfony\Component\Console\Helper\ProgressBar $bar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $channelToUpdate = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channelToUpdate === null) {
            $this->error("There is no channel with this channel_id ({$this->argument('channel_id')})");
            return;
        }

        $this->info('channel to update ' . $channelToUpdate->channel_id, 'v');
        $factory = YoutubeChannelVideos::forChannel($channelToUpdate->channel_id, 50);

        $nbVideos = count($factory->videos());
        if ($nbVideos <= 0) {
            $this->error("This channel ({$this->argument('channel_id')}) seems to have no videos.");
            return;
        }

        $this->prologue($nbVideos);

        /** for each channel video */
        array_map(function ($video) use ($channelToUpdate) {
            /** check if the video already exist in database */
            $media = Media::byMediaId($video['media_id']);
            if ($media === null) {
                $media = new Media();
                $media->media_id = $video['media_id'];
                $media->channel_id = $channelToUpdate->channel_id;
                info(
                    "Media {{$video['title']}} has been registered for channel {{$channelToUpdate->channel_name}}."
                );
            }
            // update it
            $media->title = $video['title'];
            $media->description = $video['description'];
            $media->published_at = $video['published_at'];

            /** save it */
            $media->save();

            $this->makeProgressBarProgress();
        }, $factory->videos());

        $apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed();
        Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);
    }

    protected function prologue(int $nbItems)
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar = $this->output->createProgressBar($nbItems);
            $this->bar->start();
        }
    }

    protected function epilogue()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->finish();
        }
    }

    protected function makeProgressBarProgress()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->advance();
        }
    }
}
