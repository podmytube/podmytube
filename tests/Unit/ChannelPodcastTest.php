<?php

namespace Tests\Unit;

use App\Channel;
use App\Plan;
use App\Thumb;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Tests\Traits\IsAbleToTestPodcast;

class ChannelPodcastTest extends TestCase
{
    use RefreshDatabase, IsAbleToTestPodcast;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function podcast_cover_url_should_be_default_one()
    {
        /** channel has no cover yet should be default url */
        $this->assertEquals(Thumb::defaultUrl(), $this->channel->podcastCoverUrl());
    }

    /** @test */
    public function podcast_cover_url_should_be_good()
    {
        $thumb = factory(Thumb::class)->create();
        $this->channel->setCoverFromThumb($thumb);
        $this->assertEquals($thumb->podcastUrl(), $this->channel->podcastCoverUrl());
    }

    public function testingToPodcastHeaderIsFineWithAllInformations()
    {
        $this->podcastHeaderInfosChecking($this->channel, $this->channel->podcastHeader());
    }

    public function testingToPodcastHeaderIsFineWithoutSome()
    {
        $this->channel->update([
            'podcast_title' => null,
            'podcast_copyright' => null,
            'authors' => null,
            'email' => null,
            'description' => null,
            'link' => null,
            'category_id' => null,
            'language_id' => null,
            'explicit' => true,
        ]);
        $this->podcastHeaderInfosChecking($this->channel, $this->channel->podcastHeader());
    }

    public function testToPodcastItemsForEmptyChannelShouldBeGood()
    {
        $this->assertCount(0, $this->channel->podcastItems());
        $this->assertNotNull($this->channel->podcastItems());
    }

    public function testToPodcastItemsForFreeChannelShouldBeGood()
    {
        $expectedNumberOfItems = 3;
        $freePlan = Plan::where('id', 1)->first();
        $channel = $this->createChannelWithPlan($freePlan);
        $this->addMediasToChannel($channel, 6, true);
        $this->assertCount($expectedNumberOfItems, $channel->podcastItems());
    }

    public function testToPodcastItemsForPayingChannelShouldBeGood()
    {
        $expectedNumberOfItems = 6;
        $paidPlan = Plan::find(Plan::DAILY_PLAN_ID);
        $channel = $this->createChannelWithPlan($paidPlan);
        $this->addMediasToChannel($channel, $expectedNumberOfItems, true);
        $this->assertCount($expectedNumberOfItems, $channel->podcastItems());
    }

    public function testChannelWithNoMediasToPodcastShouldBeGood()
    {
        $channelToPodcastInfos = $this->channel->toPodcast();
        /** checking header */
        $this->podcastHeaderInfosChecking($this->channel, $channelToPodcastInfos);
        /** checking items */
        $this->assertCount(0, $channelToPodcastInfos['podcastItems']);
    }

    public function testFreeChannelWithMediasToPodcastShouldBeGood()
    {
        $freePlan = Plan::find(Plan::FREE_PLAN_ID);
        $channel = $this->createChannelWithPlan($freePlan);
        $this->addMediasToChannel($channel, 5, true);
        $channelToPodcastInfos = $channel->toPodcast();

        /** checking header */
        $this->podcastHeaderInfosChecking($channel, $channelToPodcastInfos);
        /** checking items */
        $this->assertInstanceOf(Collection::class, $channelToPodcastInfos['podcastItems']);
        $this->assertCount(3, $channelToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($channelToPodcastInfos['podcastItems']);
    }

    public function testPayingChannelWithMediasToPodcastShouldBeGood()
    {
        $expectedNumberOfPodcastItems = 7;
        $payingPlan = Plan::find(Plan::WEEKLY_PLAN_ID);
        $channel = $this->createChannelWithPlan($payingPlan);
        $this->addMediasToChannel($channel, $expectedNumberOfPodcastItems, true);
        $channelToPodcastInfos = $channel->toPodcast();

        /** checking header */
        $this->podcastHeaderInfosChecking($channel, $channelToPodcastInfos);
        /** checking items */
        $this->assertInstanceOf(Collection::class, $channelToPodcastInfos['podcastItems']);
        $this->assertCount($expectedNumberOfPodcastItems, $channelToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($channelToPodcastInfos['podcastItems']);
    }

    public function testPodcastAuthorsIsOk()
    {
        $this->assertEquals($this->channel->authors, $this->channel->podcastAuthor());
    }

    public function testPodcastExplicitIsOk()
    {
        $this->channel->update(['explicit' => true]);
        $this->assertTrue($this->channel->explicit);
        $this->assertEquals('true', $this->channel->podcastExplicit());

        $this->channel->update(['explicit' => false]);
        $this->assertFalse($this->channel->explicit);
        $this->assertEquals('false', $this->channel->podcastExplicit());
    }
}
