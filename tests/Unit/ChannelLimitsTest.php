<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $plan;

    public function setUp(): void
    {
        parent::setUp();
        $this->plan = Plan::factory()->isFree()->create();
        $this->channel = Channel::factory()
            ->has(Subscription::factory()->state(['plan_id' => $this->plan->id]))
            ->create()
        ;
    }

    public function test_channel_has_not_reached_its_limits(): void
    {
        $this->assertEquals($this->plan->nb_episodes_per_month, $this->channel->numberOfEpisodesAllowed());
        $this->assertEquals(0, $this->channel->numberOfEpisodesGrabbed());
        $this->assertFalse($this->channel->hasReachedItslimit());
    }

    public function test_channel_has_reached_its_limits_this_month(): void
    {
        $this->addMediasToChannel($this->channel, $this->plan->nb_episodes_per_month, grabbed: true);
        $this->assertEquals($this->plan->nb_episodes_per_month, $this->channel->numberOfEpisodesGrabbed());
        $this->assertTrue($this->channel->hasReachedItslimit());
    }

    public function test_channel_has_reached_its_limits_on_december2019(): void
    {
        $expectedMonth = 12;
        $expectedYear = 2019;
        Media::factory()
            ->count(1)
            ->grabbedAt(Carbon::create($expectedYear, $expectedMonth, 1))
            ->create([
                'channel_id' => $this->channel->channel_id,
                'published_at' => null,
            ])
        ;
        $this->assertEquals(1, $this->channel->numberOfEpisodesGrabbed($expectedMonth, $expectedYear));
        $this->assertTrue($this->channel->hasReachedItslimit($expectedMonth, $expectedYear));
    }
}
