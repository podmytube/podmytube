<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Console\Commands\Traits\WithProgressBar;
use App\Exceptions\YoutubeNoResultsException;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Quota;
use App\Modules\ServerRole;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeCore;
use App\Youtube\YoutubeQuotas;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * get all new episodes for every active channels.
 */
class UpdateChannelsCommand extends Command
{
    use BaseCommand;
    use WithProgressBar;

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
    protected YoutubeCore $youtubeCore;
    protected Collection $channels;
    protected array $errors = [];
    protected int $mediasAdded = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return 0;
        }
        $this->prologue();

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

        $this->initProgressBar($this->channels->count());

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
                    Media::withTrashed()
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
                }, $factory->videos());

                if ($channel->hasReachedItslimit() && $channel->hasRecentlyAddedMedias()) {
                    // channel has exceeded its quota for this newly inserted media
                    // so we are warning user
                    ChannelHasReachedItsLimitsJob::dispatch($channel);
                }

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

        $this->finishProgressBar();
        $this->epilogue();

        return 0;
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
