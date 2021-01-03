<?php

namespace Tests\Unit;

use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ChannelMediasTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    public function setUp() :void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
    }

    public function testFreeChannelShouldHaveOnlyLastThirdMedias()
    {
        $expectedMediasToPublish = 3;
        $freePlan = Plan::where('id', 1)->first();

        $channel = $this->createChannelWithPlan($freePlan);

        /** adding grabbed medias */
        $this->addMediasToChannel($channel, 5, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function testOtherChannelShouldHaveAllMedias()
    {
        $expectedMediasToPublish = 5;
        $plan = Plan::where('id', '>', 1)->inRandomOrder()->first();

        $channel = $this->createChannelWithPlan($plan);

        /** adding grabbed medias */
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }

    public function testUngrabbedMediasShouldntBeIncluded()
    {
        $expectedMediasToPublish = 5;
        $plan = Plan::where('id', '>', 1)->inRandomOrder()->first();

        $channel = $this->createChannelWithPlan($plan);

        /** adding grabbed medias */
        $this->addMediasToChannel($channel, $expectedMediasToPublish, true);

        /** the ungrabbed media */
        $this->addMediasToChannel($channel);

        $this->assertCount($expectedMediasToPublish, $channel->mediasToPublish());
        $this->assertCount($expectedMediasToPublish, $channel->podcastItems());
    }
}
