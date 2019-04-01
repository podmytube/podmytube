<?php

namespace App\Services;

use App\Channel;
use App\Medias;
use Carbon\Carbon;

class MediaService
{
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
     * This function will return the number of episodes published by month for one channel.
     * @param Channel $channel_id the channel
     * @param
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getEpisodesPublishedForChannelOnMonth(Channel $channel, int $month)
    {
        if ($month < 1 && 12 < $month) {
            throw new \Exception ("Monthes are from 1 to 12 only. There is no month like {$month} for now !");
        }

        try {
            $monthBeginning = carbon::createMidnightDate(date('Y'), $month, 1);
            $monthEnding = $monthBeginning->endOfMonth();
        } catch (\Exception $e) {
            throw $e;
        }

        return Medias::publishedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();
    }

}
