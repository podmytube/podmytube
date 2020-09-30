<?php

namespace App\Console\Commands;

use App\ApiKey;
use App\Channel;
use App\Exceptions\YoutubeNoResultsException;
use App\Media;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Carbon\Carbon;
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

    /** @var \App\Youtube\YoutubeCore $youtubeCore */
    protected $youtubeCore;

    /** @var App\Channel[] $channels list of channel models */
    protected $channels = [];

    /** @var string[] $errors list of errors that occured */
    protected $errors = [];

    /** @var int $mediasAdded nb medias added during process */
    protected $mediasAdded = 0;

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
        if (!$this->checkChannelTypeToUpdate()) {
            return;
        }

        // get channel(s) to refresh (free/early/all/..)
        $this->channels = Channel::byPlanType(
            $this->argument('channelTypeToUpdate')
        );

        // no channel to refresh => nothing to do
        if (!$this->channels->count()) {
            $this->error(
                "There is no channels with this kind of plan ({$this->argument(
                    'channelTypeToUpdate'
                )})"
            );
            return;
        }

        $this->prologue($this->channels->count());

        /** for each channel */
        $this->channels->map(function ($channel) {
            try {
                $factory = YoutubeChannelVideos::forChannel($channel->channel_id, 50);
                /** for each channel video */
                array_map(function ($video) use ($channel) {
                    /** check if the video already exist in database */
                    if (!($media = Media::withTrashed()->find($video['media_id']))) {
                        $media = new Media();
                        $media->media_id = $video['media_id'];
                        $media->channel_id = $channel->channel_id;
                        info(
                            "Media {{$video['title']}} has been registered for channel {{$channel->channel_name}}."
                        );
                        $this->mediasAdded++;
                    }
                    // update it
                    $media->title = $video['title'];
                    $media->description = $video['description'];
                    $media->published_at = $video['published_at'];

                    /** save it */
                    $media->save();
                }, $factory->videos());

                $apikeysAndQuotas = YoutubeQuotas::forUrls(
                    $factory->queriesUsed()
                )->quotaConsumed();

                Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);
            } catch (YoutubeNoResultsException $exception) {
                $this->errors[] = $exception->getMessage();
            } finally {
                $this->makeProgressBarProgress();
            }
        });

        $this->epilogue();
    }

    protected function checkChannelTypeToUpdate()
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
        return true;
    }

    protected function prologue(int $prograssBarNbItems)
    {
        $this->info('Updating channels.', 'v');
        if ($this->getOutput()->isVerbose()) {
            $this->bar = $this->output->createProgressBar($prograssBarNbItems);
            $this->bar->start();
        }
    }

    protected function epilogue()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->finish();
        }

        $this->displayErrors();
        info("There were {$this->mediasAdded} media(s) added during process.");
    }

    protected function displayErrors()
    {
        if (count($this->errors) && $this->getOutput()->isVerbose()) {
            $this->line('');
            array_map(function ($error) {
                $this->error($error);
            }, $this->errors);
        }
    }

    protected function makeProgressBarProgress()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->advance();
        }
    }
}
