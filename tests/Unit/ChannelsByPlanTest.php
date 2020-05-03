<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ChannelsByPlanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        DB::table('channels')->delete();
    }

    public function testThereShouldBeOnlyPayingChannels()
    {
        $expectedPayingAccount = 2;
        $expectedFreeAccount = 3;
        factory(Subscription::class, $expectedFreeAccount)->create([
            'plan_id' => Plan::FREE_PLAN_ID,
        ]);
        factory(Subscription::class, $expectedPayingAccount)->create([
            'plan_id' => Plan::WEEKLY_PLAN_ID,
        ]);
        $this->assertCount($expectedFreeAccount, Channel::byPlanType('free'));
        $this->assertCount(
            $expectedFreeAccount + $expectedPayingAccount,
            Channel::byPlanType('all')
        );
        $this->assertCount(
            $expectedPayingAccount,
            Channel::byPlanType('paying')
        );
    }
}
