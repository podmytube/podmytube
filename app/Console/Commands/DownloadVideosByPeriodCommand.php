<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Exceptions\ChannelHasReachedItsQuotaException;
use App\Factories\DownloadMediaFactory;
use App\Models\Channel;
use App\Models\Media;
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
    use BaseCommand;

    /** @var string */
    protected $signature = 'download:channels {--period=}';

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

        $this->prologue();

        $periodOption = $this->option('period') ? Carbon::createFromFormat('Y-m', $this->option('period')) : Carbon::now();
        $period = PeriodsHelper::create($periodOption->month, $periodOption->year);

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
        $channels->each(function (Channel $channel) use ($period): void {
            try {
                if ($channel->hasReachedItslimit($period->month(), $period->year())) {
                    $message = "Channel {$channel->nameWithId()} has reached its quota. No more media will be downloaded for this period.";

                    throw new ChannelHasReachedItsQuotaException($message);
                }
                // getting all non grabbed episodes published during this period order by (with channel and subscription).
                $medias = Media::ungrabbedMediasForChannel($channel, $period);

                $nbMedias = $medias->count();
                if ($nbMedias <= 0) {
                    $message = "There is no ungrabbed medias for {$channel->nameWithId()} between " .
                        "{$period->startDate()} and {$period->endDate()}.";
                    $this->comment($message, 'v');
                    Log::notice($message);

                    return;
                }

                // for every medias
                $medias->map(function ($media): void {
                    DownloadMediaFactory::media($media, $this->getOutput()->isVerbose())->run();
                });
            } catch (ChannelHasReachedItsQuotaException $exception) {
                Log::notice($exception->getMessage());
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });

        $this->epilogue();

        return 0;
    }
}
