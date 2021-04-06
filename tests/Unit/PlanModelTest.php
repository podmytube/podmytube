<?php

namespace Tests\Unit;

use App\Plan;
use App\StripePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
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

    /** @test */
    public function by_Slug_is_ok()
    {
        $this->assertNull(Plan::bySlug('unknown'));

        $plan = Plan::bySlug('forever_free');
        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertEquals('forever_free', $plan->slug);
    }

    /** @test */
    public function by_slugs_is_ok()
    {
        $this->assertNull(Plan::bySlugs(['unknown', 'cat', 'dog']));

        $planSlugs = ['promo', 'weekly_youtuber', 'daily_youtuber', 'starter', 'professional', 'business'];

        $plans = Plan::bySlugs($planSlugs);
        $this->assertCount(count($planSlugs), $plans);
        $this->assertInstanceOf(Collection::class, $plans);
        $plans->map(function ($plan) use ($planSlugs) {
            $this->assertInstanceOf(Plan::class, $plan);
            $this->assertTrue(in_array($plan->slug, $planSlugs));
        });
    }

    /** @test */
    public function by_slugs_and_billing_frequency_should_fail()
    {
        $this->expectException(InvalidArgumentException::class);
        Plan::bySlugsAndBillingFrequency([]);
    }

    /** @test */
    public function by_slugs_and_billing_frequency_is_ok()
    {
        $catPlan = factory(Plan::class)->create(['slug' => 'cat']);
        $catYearlyBillingIWant = factory(StripePlan::class)->create(['plan_id' => $catPlan->id, 'is_yearly' => true]);
        factory(StripePlan::class)->create(['plan_id' => $catPlan->id, 'is_yearly' => false]);

        $result = Plan::bySlugsAndBillingFrequency(['cat', true]);
        $this->assertEquals($catPlan->id, $result->first()->id);
        $this->assertCount(1, $result->first()->stripePlan);
        $this->assertEquals($catYearlyBillingIWant->id, $result->first()->stripePlan->first()->id);

        /** adding another */
        $anotherCatYearlyBillingIWant = factory(StripePlan::class)->create(['plan_id' => $catPlan->id, 'is_yearly' => true]);
        $this->assertCount(2, $result->first()->stripePlan);
        $this->assertEqualsCanonicalizing(
            [$catYearlyBillingIWant->id, $anotherCatYearlyBillingIWant->id],
            $result->first()->stripePlan->pluck('id')->toArray()
        );
    }
}
