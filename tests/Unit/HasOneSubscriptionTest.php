<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HasOneSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Channel $channelWithNoPlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->channelWithNoPlan = Channel::factory()->active()->create();
    }

    /** @test */
    public function subscribe_to_plan_should_be_ok(): void
    {
        $channel = Channel::factory()->create();
        $this->assertNull($channel->subscription);

        $subscription = $channel->subscribeToPlan($this->starterPlan);
        $channel->refresh();
        $this->assertNotNull($subscription);
        $this->assertInstanceOf(Subscription::class, $subscription);

        $this->assertNotNull($channel->subscription);
        $this->assertInstanceOf(Subscription::class, $channel->subscription);

        // checking plan subscription
        $this->assertNotNull($channel->subscription->plan);
        $this->assertInstanceOf(Plan::class, $channel->subscription->plan);
        $this->assertEquals($this->starterPlan->name, $channel->subscription->plan->name);
    }

    /** @test */
    public function shortcut_to_plan_should_be_null(): void
    {
        $this->assertNull($this->channelWithNoPlan->plan);
    }

    /** @test */
    public function shortcut_to_plan_should_succeed(): void
    {
        $this->assertNotNull($this->channel->plan);
        $this->assertInstanceOf(Plan::class, $this->channel->plan);
        $this->assertEquals($this->starterPlan->name, $this->channel->plan->name);
    }

    /** @test */
    public function attaching_plan_to_channel_should_be_simple(): void
    {
        $this->markTestIncomplete('cannot save it simply');
        $this->channelWithNoPlan->plan()->save();
        $this->channelWithNoPlan->refresh();
        $this->assertNotNull($this->channelWithNoPlan->plan);
        $this->assertInstanceOf(Plan::class, $this->channelWithNoPlan->plan);
        $this->assertEquals($this->starterPlan->name, $this->channelWithNoPlan->plan->name);
    }
}
