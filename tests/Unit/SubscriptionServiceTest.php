<?php

namespace Tests\Unit;

use App\Channel;
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
        $PlanModel = getPlanForChannel(Channel::find('freeChannel'));
        $expected = 2;
        $this->assertEquals($expected, $planModel->nb_episodes_per_month,
            "Channel {{freeChannel}} should have only {$expected} episodes per month and result was {" . $planModel->nb_episodes_per_month . "}");
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
