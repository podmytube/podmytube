<?php

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\YoutubeNoResultsException;
use App\Media;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class UpdateChannelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:channels
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // update all channels
        $this->channels = Channel::active()->get();

        // no channel to refresh => nothing to do
        if (!$this->channels->count()) {
            $message = 'There is no channel to update, 🤔 strange.';
            throw new RuntimeException($message);
            $this->error($message);
            Log::debug($message);
            return;
        }

        $this->prologue($this->channels->count());

        /** for each channel */
        $this->channels->map(function ($channel) {
            try {
                Log::info("Processing channel ($channel->channel_name) ({$channel->channel_id})");
                $factory = YoutubeChannelVideos::forChannel($channel->channel_id, 50);
                if (!count($factory->videos())) {
                    Log::info("Channel {$channel->channel_name} ({$channel->channel_id}) has no video.");
                    return false;
                }

                /** for each channel video */
                array_map(function ($video) use ($channel) {
                    /** check if the video already exist in database */
                    $media = Media::withTrashed()->find($video['media_id']);
                    if ($media === null) {
                        $media = new Media();
                        $media->media_id = $video['media_id'];
                        $media->channel_id = $channel->channel_id;
                        Log::info("Media {$video['title']} has been registered for channel {$channel->channel_name}.");
                        $this->mediasAdded++;
                    }
                    // update it
                    $media->title = $video['title'];
                    $media->description = $video['description'];
                    $media->published_at = $video['published_at'];

                    /** save it */
                    $media->save();
                }, $factory->videos());

                $apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())
                    ->quotaConsumed();

                Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);
            } catch (YoutubeNoResultsException $exception) {
                $this->errors[] = $exception->getMessage();
            } finally {
                $this->makeProgressBarProgress();
            }
        });

        $this->epilogue();
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
        Log::info("There were {$this->mediasAdded} media(s) added during process.");
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
