<?php

namespace App\Console\Commands;

use App\Channel;
use App\Exceptions\NoActiveChannelException;
use App\Factories\DownloadMediaFactory;
use App\Media;
use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DownloadVideosByPeriodCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'download:channels {period?}';

    /** @var string $description */
    protected $description = 'This command will get all ungrabbed videos from all channels on specified period. Current period by default.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * no period set => using current month
         */

        $periodArgument = $this->argument('period') ? Carbon::createFromFormat('Y-m', $this->argument('period')) : Carbon::now();

        $period = PeriodsHelper::create($periodArgument->month, $periodArgument->year);

        Log::notice("Downloading ungrabbed medias for period {$period->startDate()} and {$period->endDate()}");

        /**
         * getting active channels
         */
        $channels = Channel::with(['subscription', 'subscription.plan'])
            ->where('active', '=', 1)
            ->get();

        $nbChannels = $channels->count();
        if ($nbChannels <= 0) {
            $message = "There is no active channel ! That's IMPOSSIBLE !!.";
            $this->error($message, 'v');
            Log::error($message);
            throw new NoActiveChannelException($message);
        }

        /**
         * looping on all channels
         */
        $channels->map(function ($channel) use ($period) {
            try {
                /**
                 * check if channnel has reached its quota
                 */
                if ($channel->hasReachedItslimit()) {
                    $message = "Channel {$channel->nameWithId()} has reached its quota.";
                    Log::notice($message);
                    return;
                }

                /**
                 * getting all non grabbed episodes published during this period order by (with channel and subscription)
                 */
                $medias = Media::with('channel')
                    ->where('channel_id', '=', $channel->channel_id)
                    ->publishedBetween($period->startDate(), $period->endDate())
                    ->whereNull('grabbed_at')
                    ->orderBy('published_at', 'desc')
                    ->get();

                $nbMedias = $medias->count();
                if ($nbMedias <= 0) {
                    $message = "There is no ungrabbed medias for {$channel->nameWithId()} between {$period->startDate()} and {$period->endDate()}.";
                    $this->comment($message, 'v');
                    Log::notice($message);
                    return;
                }

                /**
                 * for every medias
                 */
                $medias->map(function ($media) {
                    DownloadMediaFactory::media($media, $this->getOutput()->isVerbose())->run();
                });
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });
    }

    public function defaultPeriod()
    {
        return date('Y') . '-' . date('n');
    }
}
