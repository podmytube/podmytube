<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\MediaCleaning;
use App\Models\Media;
use App\Models\Plan;
use App\Modules\ServerRole;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CleanFreeChannelMedias extends Command
{
    public const RETENTION_PERIOD_IN_MONTH = 4;

    /** @var string The name and signature of the console command. */
    protected $signature = 'medias:clean';

    /** @var string The console command description. */
    protected $description = 'This command is cleaning free channel medias that are older than '
        . self::RETENTION_PERIOD_IN_MONTH . ' monthes';

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

        /**
         * Date before we soft deletes free channels episodes.
         */
        $removeBeforeThisDate = Carbon::now()->startOfDay()->subMonths(self::RETENTION_PERIOD_IN_MONTH);

        /**
         * get free channel medias older than RETENTION_PERIOD_IN_MONTH
         * SELECT * FROM `channels` inner join subscriptions using(channel_id) WHERE subscriptions.plan_id=1.
         */
        $mediasToDelete = Media::grabbedBefore($removeBeforeThisDate)
            ->whereHas('channel', function (Builder $query): void {
                $query->whereHas('subscription', function (Builder $query): void {
                    $query->where('plan_id', '=', Plan::FREE_PLAN_ID);
                });
            })
            ->get()
        ;

        $this->comment("There is {$mediasToDelete->count()} medias to delete", 'v');
        // remove each of them
        $mediasToDelete->each(function ($media): void {
            MediaCleaning::dispatch($media);
        });

        return 0;
    }
}
