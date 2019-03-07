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
        $result = Channel::find('freeChannel')->subscription->plan->nb_episodes_per_month;
        $expected = 2;
        $this->assertEquals($expected, $result,
            "Channel {freeChannel} should have only {{$expected}} episodes per month and result was {{$result}}");
    }

    /**
     *
     * @test
     */
    public function earlyAndDailyChannelShouldHaveOnly33EpisodesPerMonth()
    {
        foreach (['earlyChannel', 'dailyChannel'] as $channelId) {
            $result = Channel::find($channelId)->subscription->plan->nb_episodes_per_month;
            $expected = 33;
            $this->assertEquals($expected, $result,
                "Channel {{$channelId}} should have only {{$expected}} episodes per month and result was {{$result}}");
        }
    }

    /**
     *
     * @test
     */
    public function weeklyAndPromosPlansChannelShouldHaveOnly10EpisodesPerMonth()
    {
        foreach (['weeklyChannel', 'UCnf8HI3gUteF1BKAvrDO9dQ', 'UCnF1gaTK11ax2pWCIdUp8-w'] as $channelId) {
            $result = Channel::find($channelId)->subscription->plan->nb_episodes_per_month;
            $expected = 10;
            $this->assertEquals($expected, $result,
                "Channel {{$channelId}} should have only {{$expected}} episodes per month and result was {{$result}}");
        }
    }

    /**
     *
     * @test
     */
    public function accropolisPlansChannelShouldHaveOnly20EpisodesPerMonth()
    {
        $result = Channel::find('UCq80IvL314jsE7PgYsTdw7Q')->subscription->plan->nb_episodes_per_month;
        $expected = 20;
        $this->assertEquals($expected, $result,
            "Channel {UCq80IvL314jsE7PgYsTdw7Q} (Accropolis) should have only {{$expected}} episodes per month and result was {{$result}}");

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
