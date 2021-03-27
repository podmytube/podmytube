<?php

namespace Tests\Unit;

use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PlansTableSeeder;
use Tests\TestCase;

class PlanModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => PlansTableSeeder::class]);
    }

    /** @test */
    public function free_plan_should_exist_still()
    {
        $freePlan = Plan::bySlug('forever_free');
        $this->assertNotNull($freePlan);
        $this->assertInstanceOf(Plan::class, $freePlan);
    }

    /** @test */
    public function paying_plans_should_exist()
    {
        $planSlugs = ['promo', 'weekly_youtuber', 'daily_youtuber', 'starter', 'professional', 'business'];
        array_map(function ($payingSlug) {
            $plan = Plan::bySlug($payingSlug);
            $this->assertNotNull($plan);
            $this->assertInstanceOf(Plan::class, $plan);
        }, $planSlugs);
    }
}
