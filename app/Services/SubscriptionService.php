<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Channel;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * This function will return active plan for Channel object.
     *
     * @param object App\Models\Channel $channel
     *
     * @return object App\Models\Subscription
     */
    public static function getActiveSubscription(Channel $channel)
    {
        if (!isset($channel->subscription)) {
            throw new \Exception(
                "Channel {{$channel->channel_id}} has no subscription and it is not normal"
            );
        }

        return $channel->subscription;
    }

    /**
     * This function will return active plan for Channel object.
     *
     * @param object App\Models\Channel $channel
     *
     * @return object App\Models\Plan
     */
    public static function getActivePlan(Channel $channel)
    {
        if (
            !isset($channel->subscription)
            || !isset($channel->subscription->plan)
        ) {
            throw new \Exception(
                "Channel {{$channel->channel_id}} has no subscription and it is not normal"
            );
        }

        return $channel->subscription->plan;
    }

    /**
     * This function will return true if channel has subscription.
     * If no subscription it means that channel is free.
     *
     * @return bool
     */
    public static function hasSubscription(Channel $channel)
    {
        return Subscription::where(
            'channel_id',
            $channel->channel_id
        )->exists();
    }
}
