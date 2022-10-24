<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Traits\HasPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HasPlanTest extends TestCase
{
    use HasPlan;
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function has_one_plan_through_subscription_relation_should_be_fine(): void
    {
        $this->assertNull($this->channel->plan);
        $this->channel->subscribeToPlan($this->starterPlan);
        $this->channel->refresh();

        $this->assertNotNull($this->channel->plan);
        $this->assertInstanceOf(Plan::class, $this->channel->plan);
        $this->assertEquals($this->starterPlan->id, $this->channel->plan->id);
        $this->assertEquals($this->starterPlan->price, $this->channel->plan->price);
    }
}
