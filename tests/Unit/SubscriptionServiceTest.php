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
     * free channels have no subscription row in subscriptions table
     * @test
     */
    public function freeChannelShouldHaveNoSubscription()
    {
        $this->assertFalse(SubscriptionService::hasSubscription(Channel::find('freeChannel')));
    }

    /**
     * other channels should have one subscription row in subscriptions table
     * @test
     */
    public function anyOtherChannelsShouldHaveSubscription()
    {
        foreach (['weeklyChannel', 'dailyChannel'] as $channelName) {
            $this->assertTrue(SubscriptionService::hasSubscription(Channel::find($channelName)));
        }
    }
}
