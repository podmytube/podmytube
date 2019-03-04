<?php

namespace Tests\Unit;


use App\Channel;
use App\Services\ChannelPremiumToSubscriptionService;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelPremiumToSubcriptionTest extends TestCase
{
    const _EARLY_PLAN_ID = 2;
    const _WEEKLY_PLAN_ID = 5;
    const _DAILY_PLAN_ID = 6;
    const _ACCROPOLIS_PLAN_ID = 7;
    

    /**
     * 'earlyChannel',
     * 'weeklyChannel',
     * 'dailyChannel',
     */
    /**
     * Free plans do not have rows in subscription table.
     * @expectedException  App\Exceptions\FreePlanDoNotNeedSubscriptionException
     * @test
     */
    public function checkThatFreeChannelHasNoSubscription()
    {
        $channel = Channel::find('freeChannel');
        ChannelPremiumToSubscriptionService::transform($channel);        
    }

    /**
     * Early channel should have one subscription plan to 
     * @test
     */
    public function checkThatEarlyChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('earlyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_EARLY_PLAN_ID, $planId);
    }

    /**
     * Weekly youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatWeeklyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('weeklyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_WEEKLY_PLAN_ID, $planId);
    }

    /**
     * Daily youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatDailyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('dailyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_DAILY_PLAN_ID, $planId);
    }

    /**
     * Accropolis channel should have one subscription plan to 
     * @test
     */
    public function checkThatAccropolisChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('UCq80IvL314jsE7PgYsTdw7Q');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_ACCROPOLIS_PLAN_ID, $planId);
    }
}
