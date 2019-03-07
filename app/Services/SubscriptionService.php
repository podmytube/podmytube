<?php

namespace App\Services;

use App\Channel;
use App\User;
use App\Medias;
use App\Subscription;

use Carbon\Carbon;
use App\Services\ThumbService;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionService
{

    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @params Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {
    
        $monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthEnding = carbon::create()->endOfMonth();
        
        $nbMediasGrabbedThisMonth = Medias::grabbedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();

        return $nbMediasGrabbedThisMonth;
    }

    /**
     * This function will return true if channel has subscription.
     * If no subscription it means that channel is free.
     *
     * @param Channel $channel
     * @return boolean
     */
    public static function hasSubscription(Channel $channel)
    {
        return Subscription::where('channel_id', $channel->channel_id)->exists();
    }

}