<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Services\SubscriptionService;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{

    /**
     * 
     * @test
     */
    public function freeChannelShouldHaveOnlyNEpisodesPerMonth()
    {
        $this->assertFalse(true);
    }

    /**
     * other channels should have one subscription row in subscriptions table
     * @test
     */
    public function anyOtherChannelsShouldHaveSubscription()
    {
        foreach (['freeChannel', 'weeklyChannel', 'dailyChannel'] as $channelName) {
            $this->assertTrue(SubscriptionService::hasSubscription(Channel::find($channelName)));
        }
    }
}
