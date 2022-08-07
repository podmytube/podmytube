<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\StripePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Stripe\Checkout\Session;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlanModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
    }

    /** @test */
    public function free_plan_should_exist_still(): void
    {
        $freePlan = Plan::bySlug('forever_free');
        $this->assertNotNull($freePlan);
        $this->assertInstanceOf(Plan::class, $freePlan);
    }

    /** @test */
    public function paying_plans_should_exist(): void
    {
        $planSlugs = ['monthly_6', 'weekly_youtuber', 'daily_youtuber', 'starter', 'professional', 'business'];
        array_map(function ($payingSlug): void {
            $plan = Plan::bySlug($payingSlug);
            $this->assertNotNull($plan);
            $this->assertInstanceOf(Plan::class, $plan);
        }, $planSlugs);
    }

    /** @test */
    public function by__slug_is_ok(): void
    {
        $this->assertNull(Plan::bySlug('unknown'));

        $plan = Plan::bySlug('forever_free');
        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertEquals('forever_free', $plan->slug);
    }

    /** @test */
    public function by_slugs_is_ok(): void
    {
        $this->assertNull(Plan::bySlugs(['unknown', 'cat', 'dog']));

        $planSlugs = ['monthly_6', 'weekly_youtuber', 'daily_youtuber', 'starter', 'professional', 'business'];

        $plans = Plan::bySlugs($planSlugs);
        $this->assertCount(count($planSlugs), $plans);
        $this->assertInstanceOf(Collection::class, $plans);
        $plans->each(function ($plan) use ($planSlugs): void {
            $this->assertInstanceOf(Plan::class, $plan);
            $this->assertTrue(in_array($plan->slug, $planSlugs));
        });
    }

    /** @test */
    public function by_slugs_and_billing_frequency_should_fail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Plan::bySlugsAndBillingFrequency([]);
    }

    /** @test */
    public function by_slugs_and_billing_frequency_is_ok(): void
    {
        $catPlan = Plan::factory()->create(['slug' => 'cat']);
        $catYearlyBillingIWant = StripePlan::factory()->create(['plan_id' => $catPlan->id, 'is_yearly' => true]);
        StripePlan::factory()->create(['plan_id' => $catPlan->id, 'is_yearly' => false]);

        $result = Plan::bySlugsAndBillingFrequency(['cat'], true);
        $this->assertEquals($catPlan->id, $result->first()->id);
        $this->assertCount(1, $result->first()->stripePlans);
        $this->assertEquals($catYearlyBillingIWant->id, $result->first()->stripePlans->first()->id);

        /** adding another */
        $anotherCatYearlyBillingIWant = StripePlan::factory()->create(['plan_id' => $catPlan->id, 'is_yearly' => true]);
        $result = Plan::bySlugsAndBillingFrequency(['cat'], true);
        $this->assertCount(2, $result->first()->stripePlans);
        $this->assertEqualsCanonicalizing(
            [$catYearlyBillingIWant->id, $anotherCatYearlyBillingIWant->id],
            $result->first()->stripePlans->pluck('id')->toArray()
        );
    }

    /** @test */
    public function add_stripe_session_for_channel_is_running_fine(): void
    {
        $this->seedStripePlans(true);
        $plan = Plan::bySlug('starter');
        $channel = Channel::factory()->create();
        $this->assertNull($plan->stripeSession());

        $plan->addStripeSessionForChannel($channel);
        $this->assertNotNull($plan->stripeSession());
        $this->assertInstanceOf(Session::class, $plan->stripeSession());
    }
}
