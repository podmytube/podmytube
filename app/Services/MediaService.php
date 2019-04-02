<?php

namespace App\Services;

use App\Channel;
use App\Medias;
use Carbon\Carbon;

use Illuminate\Support\Collection;

class MediaService
{
    protected static $monthBeginning = null;
    protected static $monthEnding = null;
    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @param Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {

        $monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthEnding = carbon::today()->endOfMonth();

        return Medias::grabbedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();
    }

    /**
     * This function will return the episodes already grabbed for one channel during the specified month.
     * @param Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getGrabbedMediasFor(Channel $channel, int $month)
    {
        self::setMonthLimits($month);

        return Medias::grabbedBetween(self::$monthBeginning, self::$monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->get();
    }

    /**
     * This function will return the number of episodes published by month for one channel.
     * @param Channel $channel_id the channel
     * @param
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getPublishedMediasFor(Channel $channel, int $month)
    {
        self::setMonthLimits($month);

        return Medias::publishedBetween(self::$monthBeginning, self::$monthEnding)
            ->where('channel_id', $channel->channel_id)
            ->get();
    }

    protected static function setMonthLimits($month)
    {
        if ($month < 1 && 12 < $month) {
            throw new \Exception ("Monthes are from 1 to 12 only. There is no month like {$month} for now !");
        }

        try {
            self::$monthBeginning = carbon::createMidnightDate(date('Y'), $month, 1);
            self::$monthEnding = self::$monthBeginning->endOfMonth();
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

}
