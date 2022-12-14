<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\YoutubeMediaIsNotAvailableException;
use App\Factories\DownloadMediaFactory;
use App\Models\Channel;
use App\Models\Media;
use App\Modules\PeriodsHelper;
use App\Modules\ServerRole;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class DownloadVideosByChannelCommand extends Command
{
    use BaseCommand;

    /** @var string */
    protected $signature = 'download:channel {channel_id} {--period=}';

    /** @var string */
    protected $description = 'This command will get all ungrabbed videos from specified channel \\
        on specified period. Current period by default.';

    protected ProgressBar $progressBar;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return 0;
        }
        $this->prologue();

        $channel = Channel::byChannelId($this->argument('channel_id'));
        if ($channel === null) {
            $message = "There is no channel with this channel_id ({$this->argument('channel_id')})";
            $this->error($message);
            Log::error($message);

            return 1;
        }

        // no period set => using current month.
        $periodOption = $this->option('period') ? Carbon::createFromFormat('Y-m', $this->option('period')) : Carbon::now();
        $period = PeriodsHelper::create($periodOption->month, $periodOption->year);

        Log::notice(
            "Downloading ungrabbed medias for channel {$channel->channelId()} \\
            during period {$period->startDate()} and {$period->endDate()}"
        );

        if ($channel->hasReachedItslimit($period->month(), $period->year())) {
            $message = "Channel {$channel->nameWithId()} has reached its quota. No more media will be downloaded for this period.";
            $this->comment($message);
            Log::info($message);

            return 1;
        }

        /**
         * getting all non grabbed episodes published during this period order by (with channel and subscription).
         */
        $medias = Media::ungrabbedMediasForChannel($channel, $period);

        $nbMedias = $medias->count();
        if ($nbMedias <= 0) {
            $message = "There is no ungrabbed medias for this period {$period->startDate()} and {$period->endDate()}.";
            $this->comment($message, 'v');
            Log::notice($message);

            return 0;
        }

        if ($this->getOutput()->isVerbose()) {
            $this->progressBar = $this->output->createProgressBar($nbMedias);
            $this->progressBar->start();
        }

        // for every medias in db
        $medias->each(function (Media $media): void {
            try {
                DownloadMediaFactory::media($media, $this->getOutput()->isVerbose())->run();
            } catch (YoutubeMediaIsNotAvailableException|DownloadMediaTagException $exception) {
                Log::notice($exception->getMessage());
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
            if ($this->getOutput()->isVerbose()) {
                $this->progressBar->advance();
            }
        });

        if ($this->getOutput()->isVerbose()) {
            $this->progressBar->finish();
            $this->line('');
        }

        $this->epilogue();

        return 0;
    }

    public function defaultPeriod()
    {
        return date('Y') . '-' . date('n');
    }
}
