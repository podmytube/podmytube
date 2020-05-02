<?php

namespace Tests\Unit;

use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ChannelLimitsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
        $this->channel = factory(\App\Channel::class)->create();
        factory(\App\Subscription::class)->create([
            'channel_id' => $this->channel->channel_id,
            'plan_id' => \App\Plan::FREE_PLAN_ID,
        ]);
    }

    public function testChannelHasNotReachedItsLimits()
    {
        $this->assertEquals(2, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(0, $this->channel->numberOfEpisodesGrabbed());
        $this->assertFalse($this->channel->hasReachedItslimit());
    }

    public function testChannelHasReachedItsLimitsThisMonth()
    {
        factory(\App\Media::class, 2)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);
        $this->assertEquals(2, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(2, $this->channel->numberOfEpisodesGrabbed());
        $this->assertTrue($this->channel->hasReachedItslimit());
    }

    public function testChannelHasReachedItsLimitsOnDecember2019()
    {
        $expectedMonth = 12;
        $expectedYear = 2019;
        factory(\App\Media::class, 2)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => null,
            'grabbed_at' => Carbon::create(2019, 12, 1),
        ]);
        $this->assertEquals(2, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(
            2,
            $this->channel->numberOfEpisodesGrabbed(
                $expectedMonth,
                $expectedYear
            )
        );
        $this->assertTrue(
            $this->channel->hasReachedItslimit($expectedMonth, $expectedYear)
        );
    }
}
