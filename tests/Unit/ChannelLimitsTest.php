<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Subscription;
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
        $this->channel = factory(Channel::class)->create();
        factory(Subscription::class)->create([
            'channel_id' => $this->channel->channel_id,
            'plan_id' => Plan::bySlug('forever_free')->id,
        ]);
    }

    public function testChannelHasNotReachedItsLimits()
    {
        $this->assertEquals(
            2,
            $this->channel->numberOfEpisodesAllowed(),
            'We were expecting only 2 episodes allowed for free plan.'
        );
        $this->assertEquals(
            0,
            $this->channel->numberOfEpisodesGrabbed(),
            'Channel was expected to have 0 episodes grabbed.'
        );
        $this->assertFalse(
            $this->channel->hasReachedItslimit(),
            'Channel (with no grabbed eipsodes) was not expected to have reached its limits.'
        );
    }

    public function testChannelHasReachedItsLimitsThisMonth()
    {
        factory(\App\Media::class, 2)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);
        $this->assertEquals(
            2,
            $this->channel->numberOfEpisodesAllowed(),
            'We were expecting only 2 episodes allowed for free plan.'
        );
        $this->assertEquals(2, $this->channel->numberOfEpisodesGrabbed());
        $this->assertTrue(
            $this->channel->hasReachedItslimit(),
            'Channel was expected to have reached its limits.'
        );
    }

    public function testChannelHasReachedItsLimitsOnDecember2019()
    {
        $expectedMonth = 12;
        $expectedYear = 2019;
        factory(\App\Media::class, 2)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => null,
            'grabbed_at' => Carbon::create($expectedYear, $expectedMonth, 1),
        ]);
        $this->assertEquals(
            2,
            $this->channel->numberOfEpisodesAllowed(),
            'We were expecting only 2 episodes allowed for free plan.'
        );
        $this->assertEquals(
            2,
            $this->channel->numberOfEpisodesGrabbed(
                $expectedMonth,
                $expectedYear
            ),
            'Channel should have 2 episodes grabbed.'
        );
        $this->assertTrue(
            $this->channel->hasReachedItslimit($expectedMonth, $expectedYear),
            'Channel was expected to have reached its limits.'
        );
    }
}
