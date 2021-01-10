<?php

namespace Tests\Unit;

use App\Category;
use App\Channel;
use App\Plan;
use App\Podcast\PodcastItem;
use App\Thumb;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class ChannelPodcastTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
        $this->channel = $this->createChannelWithPlan();
    }

    public function testPodcastCoverUrlIsFine()
    {
        $this->assertEquals(Thumb::defaultUrl(), $this->channel->podcastCoverUrl());
        $channelWithThumb = factory(Channel::class)->create();
        $thumb = factory(Thumb::class)->create(['channel_id' => $channelWithThumb->channel_id]);
        $this->assertEquals($thumb->podcastUrl(), $channelWithThumb->podcastCoverUrl());
    }

    public function testingToPodcastHeaderIsFine()
    {
        $this->headerInfosChecking($this->channel, $this->channel->podcastHeader());
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
        $this->headerInfosChecking($this->channel, $channelToPodcastInfos);
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
        $this->headerInfosChecking($channel, $channelToPodcastInfos);
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
        $this->headerInfosChecking($channel, $channelToPodcastInfos);
        /** checking items */
        $this->assertInstanceOf(Collection::class, $channelToPodcastInfos['podcastItems']);
        $this->assertCount($expectedNumberOfPodcastItems, $channelToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($channelToPodcastInfos['podcastItems']);
    }

    public function podcastItemsChecking(Collection $podcastItems)
    {
        $podcastItems->map(
            function ($podcastItem) {
                $this->assertInstanceOf(
                    PodcastItem::class,
                    $podcastItem,
                    'PodcastItems should be a collection of PodcastItem Object'
                );
            }
        );
    }

    public function headerInfosChecking(Channel $channel, array $channelToPodcastInfos)
    {
        $expectedKeys = [
            'title',
            'link',
            'description',
            'imageUrl',
            'language',
            'category',
            'explicit',
        ];

        array_map(function ($key) use ($channelToPodcastInfos) {
            $this->assertArrayHasKey($key, $channelToPodcastInfos, "Converting a channel to a podcast header should have key {$key}.");
        }, $expectedKeys);

        $this->assertEquals($channelToPodcastInfos['title'], $channel->title());
        $this->assertEquals($channelToPodcastInfos['link'], $channel->link);
        $this->assertEquals($channelToPodcastInfos['description'], $channel->description);
        $this->assertEquals($channelToPodcastInfos['imageUrl'], $channel->podcastCoverUrl());
        $this->assertEquals($channelToPodcastInfos['language'], $channel->language->code);
        $this->assertEquals($channelToPodcastInfos['category'], $channel->category);
        $this->assertEquals($channelToPodcastInfos['explicit'], $channel->explicit);
        $this->assertInstanceOf(Category::class, $channelToPodcastInfos['category']);
    }
}
