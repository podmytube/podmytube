<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Traits\IsAbleToTestPodcast;

/**
 * @internal
 * @coversNothing
 */
class ChannelPodcastTest extends TestCase
{
    use RefreshDatabase;
    use IsAbleToTestPodcast;

    /** @var \App\Models\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function podcast_cover_url_should_be_default_one(): void
    {
        // channel has no cover yet should be default url
        $this->assertEquals(Thumb::defaultUrl(), $this->channel->podcastCoverUrl());
    }

    /** @test */
    public function podcast_cover_url_should_be_good(): void
    {
        $thumb = Thumb::factory()->create();
        $this->channel->setCoverFromThumb($thumb);
        $this->assertNotNull($thumb->podcastUrl());
        $this->assertInstanceOf(Thumb::class, $thumb);
        $this->assertEquals($thumb->podcastUrl(), $this->channel->podcastCoverUrl());
    }

    /** @test */
    public function to_podcast_header_is_fine_with_all_informations(): void
    {
        $this->podcastHeaderInfosChecking($this->channel, $this->channel->podcastHeader());
    }

    /** @test */
    public function to_podcast_header_is_fine_without_some(): void
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

    public function test_to_podcast_items_for_empty_channel_should_be_good(): void
    {
        $this->assertCount(0, $this->channel->podcastItems());
        $this->assertNotNull($this->channel->podcastItems());
    }

    public function test_to_podcast_items_for_free_channel_should_be_good(): void
    {
        $expectedNumberOfItems = 3;
        $freePlan = Plan::where('id', 1)->first();
        $channel = $this->createChannelWithPlan($freePlan);
        $this->addMediasToChannel($channel, 6, true);
        $this->assertCount($expectedNumberOfItems, $channel->podcastItems());
    }

    public function test_to_podcast_items_for_paying_channel_should_be_good(): void
    {
        $expectedNumberOfItems = 6;
        $paidPlan = Plan::find(Plan::DAILY_PLAN_ID);
        $channel = $this->createChannelWithPlan($paidPlan);
        $this->addMediasToChannel($channel, $expectedNumberOfItems, true);
        $this->assertCount($expectedNumberOfItems, $channel->podcastItems());
    }

    public function test_channel_with_no_medias_to_podcast_should_be_good(): void
    {
        $channelToPodcastInfos = $this->channel->toPodcast();
        // checking header
        $this->podcastHeaderInfosChecking($this->channel, $channelToPodcastInfos);
        // checking items
        $this->assertCount(0, $channelToPodcastInfos['podcastItems']);
    }

    public function test_free_channel_with_medias_to_podcast_should_be_good(): void
    {
        $freePlan = Plan::find(Plan::FREE_PLAN_ID);
        $channel = $this->createChannelWithPlan($freePlan);
        $this->addMediasToChannel($channel, 5, true);
        $channelToPodcastInfos = $channel->toPodcast();

        // checking header
        $this->podcastHeaderInfosChecking($channel, $channelToPodcastInfos);
        // checking items
        $this->assertInstanceOf(Collection::class, $channelToPodcastInfos['podcastItems']);
        $this->assertCount(3, $channelToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($channelToPodcastInfos['podcastItems']);
    }

    public function test_paying_channel_with_medias_to_podcast_should_be_good(): void
    {
        $expectedNumberOfPodcastItems = 7;
        $payingPlan = Plan::find(Plan::WEEKLY_PLAN_ID);
        $channel = $this->createChannelWithPlan($payingPlan);
        $this->addMediasToChannel($channel, $expectedNumberOfPodcastItems, true);
        $channelToPodcastInfos = $channel->toPodcast();

        // checking header
        $this->podcastHeaderInfosChecking($channel, $channelToPodcastInfos);
        // checking items
        $this->assertInstanceOf(Collection::class, $channelToPodcastInfos['podcastItems']);
        $this->assertCount($expectedNumberOfPodcastItems, $channelToPodcastInfos['podcastItems']);
        $this->podcastItemsChecking($channelToPodcastInfos['podcastItems']);
    }

    public function test_podcast_authors_is_ok(): void
    {
        $this->assertEquals($this->channel->authors, $this->channel->podcastAuthor());
    }

    public function test_podcast_explicit_is_ok(): void
    {
        $this->channel->update(['explicit' => true]);
        $this->assertTrue($this->channel->explicit);
        $this->assertEquals('true', $this->channel->podcastExplicit());

        $this->channel->update(['explicit' => false]);
        $this->assertFalse($this->channel->explicit);
        $this->assertEquals('false', $this->channel->podcastExplicit());
    }
}
