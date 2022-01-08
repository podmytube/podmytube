<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Media;
use App\Modules\ServerRole;
use App\Quota;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:channel {channel_id} {--limit=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update list of episodes for specific channel';

    /** @var \App\Youtube\YoutubeCore */
    protected $youtubeCore;

    /** @var array list of channel models */
    protected $channels = [];

    /** @var array list of errors that occured */
    protected $errors = [];

    /** @var \Symfony\Component\Console\Helper\ProgressBar */
    protected $bar;

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

        $channel = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channel === null) {
            $message = "There is no channel with this channel_id ({$this->argument('channel_id')})";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $this->info("Channel to update {$channel->channel_id} - limit {$this->option('limit')}", 'v');
        $factory = YoutubeChannelVideos::forChannel($channel->channel_id, $this->option('limit'));

        $nbVideos = count($factory->videos());
        if ($nbVideos <= 0) {
            $message = "This channel ({$this->argument('channel_id')}) seems to have no videos.";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $this->prologue($nbVideos);

        // for each channel video
        array_map(function ($video) use ($channel): void {
            Media::query()->updateOrCreate(
                [
                    'media_id' => $video['media_id'],
                ],
                [
                    'media_id' => $video['media_id'],
                    'channel_id' => $channel->channel_id,
                    'title' => $video['title'],
                    'description' => $video['description'],
                    'published_at' => $video['published_at'],
                ]
            );

            $this->makeProgressBarProgress();
        }, $factory->videos());

        if ($channel->hasReachedItslimit() && $channel->hasRecentlyAddedMedias()) {
            // channel has exceeded its quota for this newly inserted media
            // so we are warning user
            ChannelHasReachedItsLimitsJob::dispatch($channel);
        }

        $apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed();
        Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);

        return 0;
    }

    protected function prologue(int $nbItems)
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }

        $this->bar = $this->output->createProgressBar($nbItems);
        $this->bar->start();

        return true;
    }

    protected function epilogue()
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }
        $this->bar->finish();
    }

    protected function makeProgressBarProgress()
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }
        $this->bar->advance();
    }
}
