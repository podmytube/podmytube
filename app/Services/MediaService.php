<?php

namespace App\Services;

use App\Channel;
use App\Helpers\NumberChecker;
use App\Media;
use Carbon\Carbon;

class MediaService
{
    /**
     * return the number of episodes already grabbed for one channel.
     *
     * @param Channel $channel_id the channel
     *
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(
        Channel $channel
    ) {
        return Media::grabbedBetween(
            self::getMonthBeginning(date('n')),
            self::getMonthEnding(date('n'))
        )
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();
    }

    /**
     * Get month first day at midnight
     *
     * @param int $month the numeric version month to get the end
     * @param int|null $year the numeric version of the end
     */
    protected static function getMonthBeginning(
        int $month,
        ?int $yearParam = null
    ): Carbon {
        $year = $yearParam ?? date('Y');
        NumberChecker::isBetween($month, 1, 12);
        return Carbon::createMidnightDate($year, $month, 1);
    }

    /**
     * Get month last day at midnight
     *
     * @param int $month the numeric version month to get the end
     * @param int|null $year the numeric version of the end
     */
    protected static function getMonthEnding(
        int $month,
        ?int $yearParam = null
    ): Carbon {
        $year = $yearParam ?? date('Y');
        NumberChecker::isBetween($month, 1, 12);
        return (new Carbon("{$year}-{$month}-1"))
            ->modify('last day of this month')
            ->setTime(23, 59, 59);
    }
}
