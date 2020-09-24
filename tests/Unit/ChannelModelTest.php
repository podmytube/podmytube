<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use Carbon\Carbon;
use Tests\TestCase;
use App\Podcast\PodcastBuilder;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel channel model */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testCreatedAt()
    {
        $this->assertNotNull($this->channel->createdAt());
        $this->assertInstanceOf(Carbon::class, $this->channel->createdAt());
    }

    public function testingPodcastUrl()
    {
        $this->assertEquals(
            getenv('PODCASTS_URL') .
                DIRECTORY_SEPARATOR .
                $this->channel->channelId() .
                DIRECTORY_SEPARATOR .
                PodcastBuilder::FEED_FILENAME,
            $this->channel->podcastUrl()
        );
    }

    public function testGettingChannelsByKindShouldWorkFine()
    {
        $this->markTestIncomplete("You should search for another solution that allow to set a plan right from the ... channel creation factory ?");
        factory(Subscription::class, 5)->create(['plan_id' => Plan::FREE_PLAN_ID]);
        factory(Subscription::class, 2)->create(['plan_id' => Plan::EARLY_PLAN_ID]);
        $this->assertCount(5, Channel::freeChannels());
        $this->assertCount(2, Channel::earlyBirdsChannels());
        $this->assertCount(0, Channel::payingChannels());

        factory(Subscription::class, 2)->create(['plan_id' => Plan::WEEKLY_PLAN_ID]);
        factory(Subscription::class, 1)->create(['plan_id' => Plan::DAILY_PLAN_ID]);
        $this->assertCount(5, Channel::freeChannels());
        $this->assertCount(2, Channel::earlyBirdsChannels());
        $this->assertCount(3, Channel::payingChannels());
    }

    public function testByChannelIdIsRunningFine()
    {
        $this->assertNull(Channel::byChannelId('this_will_never_exists'));
        $channel = factory(Channel::class)->create();
        $this->assertEquals($channel->channel_id, Channel::byChannelId($channel->channel_id)->channel_id);
    }
}
