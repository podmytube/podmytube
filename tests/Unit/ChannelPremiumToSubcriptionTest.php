<?php

namespace Tests\Unit;


use App\Channel;
use App\Plan;
use App\Services\ChannelPremiumToSubscriptionService;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelPremiumToSubcriptionTest extends TestCase
{
    /**
     * free channel should have one subscription plan to 
     * @test
     */
    public function checkThatFreeChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('freeChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_FREE_PLAN_ID, $planId, "freeChannel should receive plan {".Plan::_FREE_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Early channel should have one subscription plan to 
     * @test
     */
    public function checkThatEarlyChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('earlyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_EARLY_PLAN_ID, $planId, "earlyChannel should receive plan {".Plan::_EARLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Weekly youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatWeeklyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('weeklyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_WEEKLY_PLAN_ID, $planId, "weeklyChannel should receive plan {".Plan::_WEEKLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Daily youtuber channel should have one subscription plan to 
     * @test
     */
    public function checkThatDailyYoutuberChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('dailyChannel');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_DAILY_PLAN_ID, $planId, "dailyChannel should receive plan {".Plan::_DAILY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * Accropolis channel should have one subscription plan to 
     * @test
     */
    public function checkThatAccropolisChannelWillHaveProperPlanIdGiven()
    {
        $channel = Channel::find('UCq80IvL314jsE7PgYsTdw7Q');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_ACCROPOLIS_PLAN_ID, $planId, "Accropolis should receive plan {".Plan::_ACCROPOLIS_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * old monthly premium at 6€ test
     * @test
     */
    public function checkThatOldMonthlySubscribersAreTransformedProperly()
    {
        $channel = Channel::find('UCnF1gaTK11ax2pWCIdUp8-w');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_PROMO_MONTHLY_PLAN_ID, $planId, "Old monthly subscribers should receive plan {".Plan::_PROMO_MONTHLY_PLAN_ID."} and not {{$planId}}.");
    }

    /**
     * old yearly premium at 66€ test
     * @test
     */
    public function checkThatOldYearlySubscribersAreTransformedProperly()
    {
        $channel = Channel::find('UCnf8HI3gUteF1BKAvrDO9dQ');
        $planId = ChannelPremiumToSubscriptionService::getPlanIdForChannel($channel);
        $this->assertEquals(Plan::_PROMO_YEARLY_PLAN_ID, $planId, "Old yearly subscribers should receive plan {".Plan::_PROMO_YEARLY_PLAN_ID."} and not {{$planId}}.");
    }
}
