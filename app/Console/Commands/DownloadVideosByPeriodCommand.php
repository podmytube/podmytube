<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Channel;
use App\Factories\DownloadMediaFactory;
use App\Media;
use App\Modules\PeriodsHelper;
use App\Modules\ServerRole;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * will download non episodes for every active channels.
 */
class DownloadVideosByPeriodCommand extends Command
{
    /** @var string */
    protected $signature = 'download:channels {period?}';

    /** @var string */
    protected $description = 'This command will get all ungrabbed videos from all channels on specified period. Current period by default.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        /**
         * no period set => using current month.
         */
        $periodArgument = $this->argument('period') ? Carbon::createFromFormat('Y-m', $this->argument('period')) : Carbon::now();

        $period = PeriodsHelper::create($periodArgument->month, $periodArgument->year);

        Log::notice("Downloading ungrabbed medias for period {$period->startDate()} and {$period->endDate()}");

        /**
         * getting active channels.
         */
        $channels = Channel::with(['subscription', 'subscription.plan'])
            ->where('active', '=', 1)
            ->get()
        ;

        $nbChannels = $channels->count();
        if ($nbChannels <= 0) {
            $message = "There is no active channel ! That's IMPOSSIBLE !!.";
            $this->error($message, 'v');
            Log::error($message);

            return 1;
        }

        // looping on all channels
        $channels->map(function (Channel $channel) use ($period): void {
            try {
                /**
                 * getting all non grabbed episodes published during this period order by (with channel and subscription).
                 */
                $medias = Media::with('channel')
                    ->where('channel_id', '=', $channel->channel_id)
                    ->publishedBetween($period->startDate(), $period->endDate())
                    ->whereNull('grabbed_at')
                    ->orderBy('published_at', 'desc')
                    ->get()
                ;

                $nbMedias = $medias->count();
                if ($nbMedias <= 0) {
                    $message = "There is no ungrabbed medias for {$channel->nameWithId()} between " .
                        "{$period->startDate()} and {$period->endDate()}.";
                    $this->comment($message, 'v');
                    Log::debug($message);

                    return;
                }

                // for every medias
                $medias->map(function ($media): void {
                    DownloadMediaFactory::media($media, $this->getOutput()->isVerbose())->run();
                });
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });

        return 0;
    }

    public function defaultPeriod()
    {
        return date('Y') . '-' . date('n');
    }
}
