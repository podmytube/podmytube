<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Console\Commands\Traits\WithProgressBar;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Quota;
use App\Modules\ServerRole;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChannelCommand extends Command
{
    use BaseCommand;
    use WithProgressBar;

    protected $signature = 'update:channel {channel_id} {--limit=50}';
    protected $description = 'This will update list of episodes for specific channel';

    protected array $channels = [];
    protected array $errors = [];

    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $this->prologue();

        if ($this->option('limit') === null) {
            $message = "Limit specified is not valid. If you specify --limit, you should use a numeric value IE : '--limit 10' ";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $limit = intval($this->option('limit'));

        $channel = Channel::byChannelId($this->argument('channel_id'));

        // no channel to refresh => nothing to do
        if ($channel === null) {
            $message = "There is no channel with this channel_id ({$this->argument('channel_id')})";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $this->info("Channel to update {$channel->channel_id} - limit {$limit}", 'v');
        $factory = YoutubeChannelVideos::forChannel($channel->channel_id, $limit);

        $nbVideos = count($factory->videos());
        if ($nbVideos <= 0) {
            $message = "This channel ({$this->argument('channel_id')}) seems to have no videos.";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        $this->initProgressBar($nbVideos);

        // for each channel video
        $outputTable = [];
        array_map(function ($video) use (&$outputTable, $channel): void {
            $media = Media::withTrashed()
                ->updateOrCreate(
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
                )
            ;

            $outputTable[] = [
                'media_id' => $media->media_id,
                'title' => $media->title,
                'published_at' => $media->published_at->toDateString(),
                'grabbed' => $media->hasBeenGrabbed() ? '✅' : '-',
            ];
            $this->makeProgressBarProgress();
        }, $factory->videos());

        $this->finishProgressBar();

        if ($channel->hasReachedItslimit() && $channel->hasRecentlyAddedMedias()) {
            // channel has exceeded its quota for this newly inserted media
            // so we are warning user
            ChannelHasReachedItsLimitsJob::dispatch($channel);
        }

        $apikeysAndQuotas = YoutubeQuotas::forUrls($factory->queriesUsed())->quotaConsumed();
        Quota::saveScriptConsumption(pathinfo(__FILE__, PATHINFO_BASENAME), $apikeysAndQuotas);

        if ($this->isVerbose()) {
            $this->table(
                ['Media ID', 'Title', 'Published at', 'Grabbed'],
                $outputTable
            );
        }

        $this->epilogue();

        return Command::SUCCESS;
    }
}
