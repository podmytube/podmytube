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
     * This function will return active plan for Channel object
     *
     * @param object App\Channel $channel
     * @return object App\Subscription
     */
    public static function getActiveSubscription(Channel $channel){
        if (!isset($channel->subscription)){
            throw new \Exception("Channel {{$channel->channel_id}} has no subscription and it is not normal");
        }
        return $channel->subscription;
    }


    /**
     * This function will return active plan for Channel object
     *
     * @param object App\Channel $channel
     * @return object App\Plan
     */
    public static function getActivePlan(Channel $channel){
        if (!isset($channel->subscription) || !isset($channel->subscription->plan)){
            throw new \Exception("Channel {{$channel->channel_id}} has no subscription and it is not normal");
        }
        return $channel->subscription->plan;
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