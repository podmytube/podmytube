<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\Subscription;
use App\Traits\HasOneSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HasOneSubscriptionTest extends TestCase
{
    use HasOneSubscription;
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $freePlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->freePlan = Plan::factory()->isFree()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function subscription_relation_should_be_fine(): void
    {
        // no subscription
        $this->assertNull($this->channel->subscription);
        $this->channel
            ->subscription()
            ->save(
                Subscription::factory()
                    ->plan($this->starterPlan)
                    ->create()
            )
        ;

        $this->channel->refresh();
        $this->assertNotNull($this->channel->subscription);
        $this->assertInstanceOf(Subscription::class, $this->channel->subscription);
        $this->assertEquals($this->starterPlan->id, $this->channel->subscription->plan_id);
    }

    /** @test */
    public function has_one_subscription_should_be_fine(): void
    {
        // no subscription
        $this->assertFalse($this->channel->hasSubscription());

        $this->channel
            ->subscription()
            ->save(
                Subscription::factory()
                    ->plan($this->starterPlan)
                    ->create()
            )
        ;

        $this->channel->refresh();
        $this->assertTrue($this->channel->hasSubscription());
    }

    /** @test */
    public function add_subscribed_plan_should_be_fine(): void
    {
        $this->assertNull($this->channel->subscription);
        $this->channel->subscribeToPlan($this->starterPlan);

        $this->channel->refresh();
        $this->assertNotNull($this->channel->subscription);
        $this->assertInstanceOf(Subscription::class, $this->channel->subscription);
        $this->assertEquals($this->starterPlan->id, $this->channel->subscription->plan_id);
    }

    /** @test */
    public function plan_should_be_accessible_through_subscription(): void
    {
        $this->assertNull($this->channel->subscription);
        $this->channel->subscribeToPlan($this->starterPlan);

        $this->channel->refresh();
        $this->assertNotNull($this->channel->subscription);
        $this->assertInstanceOf(Subscription::class, $this->channel->subscription);
        $this->assertEquals($this->starterPlan->id, $this->channel->subscription->plan_id);
    }
}
