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

    /** @var array $channels list of channel models */
    protected $channels = [];

    protected $errors = [];

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
        if ($this->checkChannelTypeToUpdate() === false) {
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
                $channelVideos = new YoutubeChannelVideos();

                /** for each channel video */
                array_map(function ($video) use ($channel) {
                    /** check if the video already exist in database */
                    if (!($media = Media::find($video['media_id']))) {
                        $media = new Media();
                        $media->media_id = $video['media_id'];
                        $media->channel_id = $channel->channel_id;
                    }
                    // update it
                    $media->title = $video['title'];
                    $media->description = $video['description'];
                    $media->published_at = $video['published_at'];

                    /** save it */
                    $media->save();
                }, $channelVideos->forChannel($channel->channel_id)->videos());

                $apikeysAndQuotas = YoutubeQuotas::forUrls(
                    $channelVideos->queriesUsed()
                )->quotaConsumed();

                $dataToInsert = [];
                foreach ($apikeysAndQuotas as $apikey => $quota) {
                    $dataToInsert[] = [
                        'apikey_id' => ApiKey::where(
                            'apikey',
                            '=',
                            $apikey
                        )->first()->id,
                        'script' => pathinfo(__FILE__, PATHINFO_BASENAME),
                        'quota_used' => $quota,
                        'created_at' => Carbon::now(),
                    ];
                }
                Quota::insert($dataToInsert);
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
