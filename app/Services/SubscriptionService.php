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