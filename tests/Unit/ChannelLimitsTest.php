<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use PlansTableSeeder;
use Tests\TestCase;

class ChannelLimitsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected const FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES = 1;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => PlansTableSeeder::class]);
        $this->channel = factory(Channel::class)->create();
        factory(Subscription::class)->create([
            'channel_id' => $this->channel->channel_id,
            'plan_id' => Plan::bySlug('forever_free')->id,
        ]);
    }

    public function testChannelHasNotReachedItsLimits()
    {
        $this->assertEquals(self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(0, $this->channel->numberOfEpisodesGrabbed());
        $this->assertFalse($this->channel->hasReachedItslimit());
    }

    public function testChannelHasReachedItsLimitsThisMonth()
    {
        $this->addMediasToChannel($this->channel, self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, true);
        $this->assertEquals(self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, $this->channel->numberOfEpisodesGrabbed());
        $this->assertTrue($this->channel->hasReachedItslimit());
    }

    public function testChannelHasReachedItsLimitsOnDecember2019()
    {
        $expectedMonth = 12;
        $expectedYear = 2019;
        factory(\App\Media::class, 1)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => null,
            'grabbed_at' => Carbon::create($expectedYear, $expectedMonth, 1),
        ]);
        $this->assertEquals(1, $this->channel->numberOfEpisodesGrabbed($expectedMonth, $expectedYear));
        $this->assertTrue($this->channel->hasReachedItslimit($expectedMonth, $expectedYear));
    }
}
