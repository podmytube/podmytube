<?php

namespace Tests\Unit;

use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PlanModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    public function testFreePlansShouldWorkFine()
    {
        $expectedPlanIds = [
            Plan::FREE_PLAN_ID
        ];

        $this->assertEqualsCanonicalizing(
            $expectedPlanIds,
            Plan::free()->get()->pluck('id')->toArray()
        );
    }

    public function testPayingPlansShouldWorkFine()
    {
        $expectedPlanIds = [
            Plan::PROMO_MONTHLY_PLAN_ID,
            Plan::PROMO_YEARLY_PLAN_ID,
            Plan::WEEKLY_PLAN_ID,
            Plan::DAILY_PLAN_ID,
            Plan::ACCROPOLIS_PLAN_ID,
            Plan::WEEKLY_PLAN_PROMO_ID,
            Plan::DAILY_PLAN_PROMO_ID,
        ];

        $this->assertEqualsCanonicalizing(
            $expectedPlanIds,
            Plan::paying()->get()->pluck('id')->toArray()
        );
    }
}
