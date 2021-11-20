<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelLimitsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected const FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES = 1;

    /** @var \App\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->channel = factory(Channel::class)->create();
        factory(Subscription::class)->create([
            'channel_id' => $this->channel->channel_id,
            'plan_id' => Plan::bySlug('forever_free')->id,
        ]);
    }

    public function test_channel_has_not_reached_its_limits(): void
    {
        $this->assertEquals(self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(0, $this->channel->numberOfEpisodesGrabbed());
        $this->assertFalse($this->channel->hasReachedItslimit());
    }

    public function test_channel_has_reached_its_limits_this_month(): void
    {
        $this->addMediasToChannel($this->channel, self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, true);
        $this->assertEquals(self::FREE_PLAN_NUMBER_OF_AUTHORIZED_EPISODES, $this->channel->numberOfEpisodesGrabbed());
        $this->assertTrue($this->channel->hasReachedItslimit());
    }

    public function test_channel_has_reached_its_limits_on_december2019(): void
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
