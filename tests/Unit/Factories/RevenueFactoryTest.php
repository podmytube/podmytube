<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\RevenueFactory;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RevenueFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function revenue_factory_is_fine(): void
    {
        // free channels
        $freePlan = $this->getFreePlan();
        $nbFreeChannels = 10;
        $this->createChannelsWithPlan($freePlan, $nbFreeChannels);

        $expectedRevenues = $nbFreeChannels * $freePlan->price;
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // starter channels
        $starterPlan = $this->getPlanBySlug('starter');
        $nbStarterChannels = 5;
        $expectedRevenues += $nbStarterChannels * $starterPlan->price;

        $this->createChannelsWithPlan($starterPlan, $nbStarterChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // pro channels
        $proPlan = $this->getPlanBySlug('professional');
        $nbProChannels = 3;
        $expectedRevenues += $nbProChannels * $proPlan->price;

        $this->createChannelsWithPlan($proPlan, $nbProChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // business channels
        $businessPlan = $this->getPlanBySlug('business');
        $nbBusinessChannels = 2;
        $expectedRevenues += $nbBusinessChannels * $businessPlan->price;

        $this->createChannelsWithPlan($businessPlan, $nbBusinessChannels);
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());

        // adding inactive channel should not change result
        $inactiveChannel = $this->createChannelWithPlan($starterPlan);
        Subscription::where('channel_id', '=', $inactiveChannel->channelId())
            ->update(['ends_at' => now()->submonth()])
        ;
        $this->assertEquals($expectedRevenues, RevenueFactory::init()->get());
    }
}
