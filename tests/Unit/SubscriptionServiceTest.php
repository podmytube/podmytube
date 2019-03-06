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
    public function freeChannelShouldHaveOnly2EpisodesPerMonth()
    {
        $planModel = SubscriptionService::getPlanForChannel(Channel::find('freeChannel'));
        $expected = 2;
        $this->assertEquals($expected, $planModel->nb_episodes_per_month,
            "Channel {freeChannel} should have only {{$expected}} episodes per month and result was {{$planModel->nb_episodes_per_month}}");
    }

    /**
     *
     * @test
     */
    public function earlyAndDailyChannelShouldHaveOnly33EpisodesPerMonth()
    {
        foreach (['earlyChannel', 'dailyChannel'] as $channelId) {
            $planModel = SubscriptionService::getPlanForChannel(Channel::find($channelId));
            $expected = 33;
            $this->assertEquals($expected, $planModel->nb_episodes_per_month,
                "Channel {{$channelId}} should have only {{$expected}} episodes per month and result was {{$planModel->nb_episodes_per_month}}");
        }
    }

    /**
     *
     * @test
     */
    public function weeklyAndPromosPlansChannelShouldHaveOnly10EpisodesPerMonth()
    {
        foreach (['weeklyChannel', 'UCnf8HI3gUteF1BKAvrDO9dQ', 'UCnF1gaTK11ax2pWCIdUp8-w'] as $channelId) {
            $planModel = SubscriptionService::getPlanForChannel(Channel::find($channelId));
            $expected = 10;
            $this->assertEquals($expected, $planModel->nb_episodes_per_month,
                "Channel {{$channelId}} should have only {{$expected}} episodes per month and result was {{$planModel->nb_episodes_per_month}}");
        }
    }

    /**
     *
     * @test
     */
    public function accropolisPlansChannelShouldHaveOnly20EpisodesPerMonth()
    {
        $planModel = SubscriptionService::getPlanForChannel(Channel::find('UCq80IvL314jsE7PgYsTdw7Q'));
        $expected = 20;
        $this->assertEquals($expected, $planModel->nb_episodes_per_month,
            "Channel {UCq80IvL314jsE7PgYsTdw7Q} (Accropolis) should have only {{$expected}} episodes per month and result was {{$planModel->nb_episodes_per_month}}");

    }

    /**
     * other channels should have one subscription row in subscriptions table
     * @test
     */
    public function anyOtherChannelsShouldHaveSubscription()
    {
        foreach (['freeChannel', 'weeklyChannel', 'dailyChannel'] as $channelId) {
            $this->assertTrue(SubscriptionService::hasSubscription(Channel::find($channelId)));
        }
    }
}
