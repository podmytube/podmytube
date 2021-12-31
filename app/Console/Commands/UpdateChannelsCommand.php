<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\YoutubeNoResultsException;
use App\Media;
use App\Modules\ServerRole;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * get all new episodes for every active channels.
 */
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

    /** @var \App\Youtube\YoutubeCore */
    protected $youtubeCore;

    /** @var array list of channel models */
    protected $channels = [];

    /** @var array list of errors that occured */
    protected $errors = [];

    /** @var int nb medias added during process */
    protected $mediasAdded = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        // update all channels
        $this->channels = Channel::active()->get();

        // no channel to refresh => nothing to do
        if (!$this->channels->count()) {
            $message = 'There is no channel to update, 🤔 strange.';

            throw new RuntimeException($message);
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $this->prologue($this->channels->count());

        // for each channel
        $this->channels->each(function (Channel $channel) {
            try {
                Log::info("Processing channel {$channel->nameWithId()}");
                $factory = YoutubeChannelVideos::forChannel($channel->channel_id, 50);
                if (!count($factory->videos())) {
                    Log::info("Channel {$channel->channel_name} ({$channel->channel_id}) has no video.");

                    return false;
                }

                // for each channel video
                array_map(function (array $video) use ($channel): void {
                    // check if the video already exist in database
                    Media::query()
                        ->updateOrCreate(
                            [
                                'media_id' => $video['media_id'],
                            ],
                            [
                                'channel_id' => $channel->channel_id,
                                'title' => $video['title'],
                                'description' => $video['description'],
                                'published_at' => $video['published_at'],
                            ]
                        )
                    ;
                }, $factory->videos());

                $apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())
                    ->quotaConsumed()
                ;

                Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);
            } catch (YoutubeNoResultsException $exception) {
                $this->errors[] = $exception->getMessage();
            } finally {
                $this->makeProgressBarProgress();
            }
        });

        $this->epilogue();

        return 0;
    }

    protected function prologue(int $prograssBarNbItems): void
    {
        $this->info('Updating channels.', 'v');
        if ($this->getOutput()->isVerbose()) {
            $this->bar = $this->output->createProgressBar($prograssBarNbItems);
            $this->bar->start();
        }
    }

    protected function epilogue(): void
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->finish();
        }

        $this->displayErrors();
        Log::info("There were {$this->mediasAdded} media(s) added during process.");
    }

    protected function displayErrors(): void
    {
        if (count($this->errors) && $this->getOutput()->isVerbose()) {
            $this->line('');
            array_map(function ($error): void {
                $this->error($error);
            }, $this->errors);
        }
    }

    protected function makeProgressBarProgress(): void
    {
        if ($this->getOutput()->isVerbose()) {
            $this->bar->advance();
        }
    }
}
