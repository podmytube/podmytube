<?php

namespace App\Console\Commands;

use App\Factories\DownloadMediaFactory;
use App\Media;
use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DownloadVideosByChannelCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'download:channel {channel_id} {period?}';

    /** @var string $description */
    protected $description = 'This command will get all ungrabbed videos from specified channel on specified period. Current period by default.';

    protected $progressBar;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $channelId = $this->argument('channel_id');
        /**
         * no period set => using current month
         */
        $periodArgument = $this->argument('period') ? Carbon::createFromFormat('Y-m', $this->argument('period')) : Carbon::now();

        $period = PeriodsHelper::create($periodArgument->month, $periodArgument->year);

        Log::notice("Downloading ungrabbed medias for channel {$channelId} during period {$period->startDate()} and {$period->endDate()}");
        /**
         * getting all non grabbed episodes published during this period order by (with channel and subscription)
         */
        $medias = Media::with('channel')
            ->publishedBetween($period->startDate(), $period->endDate())
            ->where('channel_id', '=', $channelId)
            ->whereNull('grabbed_at')->get();

        $nbMedias = $medias->count();
        if ($nbMedias <= 0) {
            $message = "There is no ungrabbed medias for this period {$period->startDate()} and {$period->endDate()}.";
            $this->comment($message, 'v');
            Log::notice($message);
            return;
        }

        if ($this->getOutput()->isVerbose()) {
            $this->progressBar = $this->output->createProgressBar($nbMedias);
            $this->progressBar->start();
        }

        /**
         * for every medias in db
         */
        foreach ($medias as $media) {
            try {
                DownloadMediaFactory::media($media, $this->getOutput()->isVerbose())->run();
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
            if ($this->getOutput()->isVerbose()) {
                $this->progressBar->advance();
            }
        }
        if ($this->getOutput()->isVerbose()) {
            $this->progressBar->finish();
            $this->line('');
        }
    }

    public function defaultPeriod()
    {
        return date('Y') . '-' . date('n');
    }
}