<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelMediasTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
    }

    public function test_free_channel_should_have_only_last_third_medias(): void
    {
        $expectedMediasToPublish = 3;
        $freePlan = Plan::where('id', 1)->first();

        $channel = $this->createChannelWithPlan($freePlan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, 5, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function test_other_channel_should_have_all_medias(): void
    {
        $expectedMediasToPublish = 5;
        $plan = Plan::where('id', '>', 1)->inRandomOrder()->first();

        $channel = $this->createChannelWithPlan($plan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function test_ungrabbed_medias_shouldnt_be_included(): void
    {
        $expectedMediasToPublish = 5;
        $plan = Plan::where('id', '>', 1)->inRandomOrder()->first();

        $channel = $this->createChannelWithPlan($plan);

        // adding grabbed medias
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        // the ungrabbed media
        $this->addMediasToChannel($channel);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }
}
