<?php

namespace Tests\Unit;


use App\Channel;
use App\Services\ChannelPremiumToSubscriptionService;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelPremiumToSubcriptionTest extends TestCase
{
    // cont _FREE_PLAN_ID = 1; not used here - no subscription
    const _EARLY_PLAN_ID = 2;
    const _PROMO_MONTHLY_PLAN_ID = 3;
    const _PROMO_YEARLY_PLAN_ID = 4;
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
        $this->assertEquals(self::_EARLY_PLAN_ID, $planId, "earlyChannel should receive plan {".self::_EARLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Weekly youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatWeeklyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('weeklyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_WEEKLY_PLAN_ID, $planId, "weeklyChannel should receive plan {".self::_WEEKLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Daily youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatDailyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('dailyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_DAILY_PLAN_ID, $planId, "dailyChannel should receive plan {".self::_DAILY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Accropolis channel should have one subscription plan to 
     * @test
     */
    public function checkThatAccropolisChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('UCq80IvL314jsE7PgYsTdw7Q');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_ACCROPOLIS_PLAN_ID, $planId, "Accropolis should receive plan {".self::_ACCROPOLIS_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * old monthly premium at 6€ test
     * @test
     */
    public function checkThatOldMonthlySubscribersAreTransformedProperly()
    {
        $channel = Channel::find('UCnF1gaTK11ax2pWCIdUp8-w');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_PROMO_MONTHLY_PLAN_ID, $planId, "Old monthly subscribers should receive plan {".self::_PROMO_MONTHLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * old yearly premium at 66€ test
     * @test
     */
    public function checkThatOldYearlySubscribersAreTransformedProperly()
    {
        $channel = Channel::find('UCnf8HI3gUteF1BKAvrDO9dQ');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(self::_PROMO_YEARLY_PLAN_ID, $planId, "Old yearly subscribers should receive plan {".self::_PROMO_YEARLY_PLAN_ID."} and not {{$planId}}.");
    }
}
