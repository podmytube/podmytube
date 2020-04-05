<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Subscription;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelExtractMethodsTest extends TestCase
{
    use RefreshDatabase;

    public function testingSpecifisChannelsExtractorsAreWorkingFine()
    {
        // free
        $expectedNumberOfFreeChannels = 55;
        // early
        $expectedNumberOfEarlyChannels = 12;
        // paying
        $expectedNumberOfPayingChannels = 10;
        // total is the sum of all previous
        $expectedTotalNumberOfChannels = $expectedNumberOfFreeChannels + $expectedNumberOfEarlyChannels + $expectedNumberOfPayingChannels;
        // creating free channels
        factory(Subscription::class, $expectedNumberOfFreeChannels)->create(['plan_id' => Plan::FREE_PLAN_ID]);

        // creating early birds channels
        factory(Subscription::class, $expectedNumberOfEarlyChannels)->create(['plan_id' => Plan::EARLY_PLAN_ID]);

        // creating paying channels
        factory(Subscription::class, $expectedNumberOfPayingChannels)
            ->create([
                'plan_id' => rand(Plan::PROMO_MONTHLY_PLAN_ID, Plan::ACCROPOLIS_PLAN_ID)
            ]);
        $this->assertCount($expectedNumberOfFreeChannels, Channel::freeChannels());
        $this->assertCount($expectedNumberOfPayingChannels, Channel::payingChannels());
        $this->assertCount($expectedNumberOfEarlyChannels, Channel::earlyBirdsChannels());
        $this->assertCount($expectedTotalNumberOfChannels, Channel::allActiveChannels());
    }
}
