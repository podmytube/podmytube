<?php

namespace App\Services;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MediaService
{
    /**
     * This function will get list of medias ordered by status (grabbed or not).
     * @param Channel $channel the channel object we want medias
     * @param integer $month to define wanted period (by default current month)
     * @param integer $year to define wanted period (by default current year)
     * @return array
     */
    public static function getMediasStatusByPeriodForChannel(
        Channel $channel,
        $month = null,
        $year = null
    ) {
        if (empty($month)) {
            $month = date('n');
        }
        if (empty($year)) {
            $year = date('Y');
        }
        try {
            return Media::publishedBetween(
                self::getMonthBeginning($month, $year),
                self::getMonthEnding($month, $year)
            )
                ->select(
                    'media_id',
                    'title',
                    DB::raw('if(ISNULL(grabbed_at), 0, 1) as grabbed')
                )
                ->where('channel_id', $channel->channel_id)
                ->orderBy('published_at', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @param Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(
        Channel $channel
    ) {
        try {
            return Media::grabbedBetween(
                self::getMonthBeginning(date('n')),
                self::getMonthEnding(date('n'))
            )
                ->whereNotNull('grabbed_at')
                ->where('channel_id', $channel->channel_id)
                ->count();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * This function will return the episodes already grabbed for one channel during the specified month.
     * @param Channel $channel_id the channel
     * @param integer $month month num wanted
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getGrabbedMediasFor(
        Channel $channel,
        int $month,
        int $year = null
    ) {
        try {
            return Media::grabbedBetween(
                self::getMonthBeginning($month, $year),
                self::getMonthEnding($month, $year)
            )
                ->whereNotNull('grabbed_at')
                ->where('channel_id', $channel->channel_id)
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * This function will return the episodes list published for one channel for one period.
     * @param Channel $channel_id the channel
     * @param int $month the numeric version of the month to obtain
     * @param int|null $year the numeric version of the month to obtain (default will be current year)
     * @return model the list of published episodes during this month for this channel
     */
    public static function getPublishedMediasFor(
        Channel $channel,
        int $month,
        int $year = null
    ) {
        try {
            return Media::publishedBetween(
                self::getMonthBeginning($month, $year),
                self::getMonthEnding($month, $year)
            )
                ->where('channel_id', $channel->channel_id)
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * This function will set one month beginning period (IE last day of previous month at 00h00)
     * @param int $month the numeric version month to get the end
     * @param int|null $year the numeric version of the end
     */
    protected static function getMonthBeginning(
        int $month,
        $year = null
    ): Carbon {
        if (1 > $month || $month > 12) {
            throw new \Exception(
                "Monthes are from 1 to 12 only. There is no month like {$month} for now !"
            );
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $startDate = carbon::createFromDate($year, $month);

        try {
            return carbon::createMidnightDate($year, $month, 1);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * This function will set one month ending period (IE last day of the month at 23h59)
     * @param int $month the numeric version month to get the end
     * @param int|null $year the numeric version of the end
     */
    protected static function getMonthEnding($month, $year = null): Carbon
    {
        if ($month < 1 && 12 < $month) {
            throw new \Exception(
                "Monthes are from 1 to 12 only. There is no month like {$month} for now !"
            );
        }

        if (empty($year)) {
            $year = date('Y');
        }

        try {
            return (new Carbon("$year-$month-1"))
                ->modify('last day of this month')
                ->setTime(23, 59, 59);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
