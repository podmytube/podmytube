<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\RevenueFactory;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RevenueFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected Plan $freePlan;
    protected Plan $starterPlan;
    protected Plan $proPlan;
    protected Plan $bizPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->freePlan = Plan::factory()->isFree()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->proPlan = Plan::factory()->name('professional')->create(['price' => 29]);
        $this->bizPlan = Plan::factory()->name('business')->create(['price' => 79]);
    }

    /** @test */
    public function revenue_factory_is_fine(): void
    {
        // free channels
        $nbFreeChannels = 10;
        $this->createChannelsWithPlan($this->freePlan, $nbFreeChannels);

        $expectedRevenues = $nbFreeChannels * $this->freePlan->price;
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // starter channels
        $nbStarterChannels = 5;
        $expectedRevenues += $nbStarterChannels * $this->starterPlan->price;

        $this->createChannelsWithPlan($this->starterPlan, $nbStarterChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // pro channels
        $nbProChannels = 3;
        $expectedRevenues += $nbProChannels * $this->proPlan->price;

        $this->createChannelsWithPlan($this->proPlan, $nbProChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // business channels
        $nbBusinessChannels = 2;
        $expectedRevenues += $nbBusinessChannels * $this->bizPlan->price;

        $this->createChannelsWithPlan($this->bizPlan, $nbBusinessChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // adding inactive channel should not change result
        $inactiveChannel = $this->createChannelWithPlan($this->starterPlan);
        Subscription::where('channel_id', '=', $inactiveChannel->channelId())
            ->update(['ends_at' => now()->submonth()])
        ;
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());
    }
}
