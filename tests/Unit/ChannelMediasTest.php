<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelMediasTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected Plan $freePlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->freePlan = Plan::factory()->isFree()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    public function test_free_channel_should_have_only_last_third_medias(): void
    {
        $expectedMediasToPublish = 3;

        $channel = $this->createChannelWithPlan($this->freePlan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, 5, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function test_other_channel_should_have_all_medias(): void
    {
        $expectedMediasToPublish = 5;
        $channel = $this->createChannelWithPlan($this->starterPlan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function test_ungrabbed_medias_shouldnt_be_included(): void
    {
        $expectedMediasToPublish = 5;

        $channel = $this->createChannelWithPlan($this->starterPlan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        // the ungrabbed media
        $this->addMediasToChannel($channel);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }
}
