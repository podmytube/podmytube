<?php

namespace Tests\Unit;

use App\Plan;
use App\Services\PlanService;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    const _STRIPE_PLANS = [
        // Plan::_FREE_PLAN_ID=>null,
        // Plan::_EARLY_PLAN_ID=>null,
        // plan name => [dev_stripe_plan_id, prod_stripe_plan_id]
        Plan::_PROMO_MONTHLY_PLAN_ID => ['plan_EfYDgsuNMdj8Sb', 'plan_EcuGg9SyUBw97i'],
        Plan::_PROMO_YEARLY_PLAN_ID => ['plan_EfYBFztmlQ3u4C', 'plan_EcuJ2npV5EMrCg'],
        Plan::_WEEKLY_PLAN_ID => ['plan_EfudBu6TCXHWEg', 'plan_EaIv2XTMGtuY5g'],
        Plan::_DAILY_PLAN_ID => ['plan_EfuceKVUwJTt5O', 'plan_DFsB9U76WaSaR3'],
        Plan::_ACCROPOLIS_PLAN_ID => ['plan_EfubS6xkc5amyO', 'plan_Ecv3k67W6rsSKk'],
    ];

    /**
     * Asking for 2 plans in prod mode should return one array with the 2 stripe plans.
     * @test
     */
    public function gettingSomeProdPlansShouldBeGood()
    {
        $result = PlanService::getStripePlans([
            Plan::_PROMO_YEARLY_PLAN_ID,
            Plan::_DAILY_PLAN_ID,
        ]);
        $expected = [
            Plan::_PROMO_YEARLY_PLAN_ID=>'plan_EcuJ2npV5EMrCg',
            Plan::_DAILY_PLAN_ID=>'plan_DFsB9U76WaSaR3',
        ];
        $this->assertEquals($expected, $result,
            "Asking for some stripe plans in prod mode has failed !");

    }

    /**
     * Asking for 2 plans in dev mode should return one array with the 2 stripe plans.
     * @test
     */
    public function gettingSomeDevPlansShouldBeGood()
    {
        $result = PlanService::getStripePlans([
            Plan::_PROMO_MONTHLY_PLAN_ID,
            Plan::_WEEKLY_PLAN_ID,
        ], false);
        $expected = [
            Plan::_PROMO_MONTHLY_PLAN_ID=>'plan_EfYDgsuNMdj8Sb',
            Plan::_WEEKLY_PLAN_ID=>'plan_EfudBu6TCXHWEg',
        ];
        $this->assertEquals($expected, $result,
            "Asking for some stripe plans in dev mode has failed !");

    }

    /**
     * Checking each plans in DEV mode
     * @test
     */
    public function stripePlansShouldBeValidInDev()
    {
        foreach (self::_STRIPE_PLANS as $plan_id => $stripeItem) {
            $result = PlanService::getStripePlan($plan_id, false);
            $expected = $stripeItem[0];
            $this->assertEquals($expected, $result,
                "For plan {{$plan_id}} stripe_id should be {{$expected}} in dev mode and result was {{$result}}");
        }
    }

    /**
     * Checking each plans in prod mode
     * @test
     */
    public function stripePlansShouldBeValidInProdToo()
    {
        foreach (self::_STRIPE_PLANS as $plan_id => $stripeItem) {
            $result = PlanService::getStripePlan($plan_id, true);
            $expected = $stripeItem[1];
            $this->assertEquals($expected, $result,
                "For plan {{$plan_id}} stripe_id should be {{$expected}} in prod mode and result was {{$result}}");
        }
    }

    /**
     * @test
     */
    public function InvalidPlanShouldThrowInvalidArgumentException()
    {
        $this->expectException(\Exception::class);
        $result = PlanService::getStripePlan(99999);
    }
}
