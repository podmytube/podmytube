<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Plan;
use Carbon\Carbon;
use Tests\TestCase;
use App\Podcast\PodcastBuilder;
use App\Subscription;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

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
            config('app.podcasts_url') .
                '/' .
                $this->channel->channelId() .
                '/' .
                PodcastBuilder::FEED_FILENAME,
            $this->channel->podcastUrl()
        );
    }

    /**
     * @todo
     */
    public function testGettingChannelsByKindShouldWorkFine()
    {
        $this->markTestIncomplete('You should search for another solution that allow to set a plan right from the ... channel creation factory ?');
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
        $this->assertEquals($this->channel->channel_id, Channel::byChannelId($this->channel->channel_id)->channel_id);
    }

    public function testingIsFreeShouldBeTrue()
    {
        factory(Subscription::class)->create(['channel_id' => $this->channel->channel_id, 'plan_id' => Plan::FREE_PLAN_ID]);
        $this->assertTrue($this->channel->isFree());
    }

    public function testingIsFreeShouldBeFalse()
    {
        factory(Subscription::class)->create(['channel_id' => $this->channel->channel_id, 'plan_id' => Plan::WEEKLY_PLAN_ID]);
        $this->assertFalse($this->channel->isFree());
    }

    public function testingNextMediaIdShouldBeOk()
    {
        $media = factory(Media::class, 10)->create(['channel_id' => $this->channel->channel_id]);
        $expectedResult = substr(Str::slug($this->channel->channel_name), 0, 20) . '-11';
        $this->assertEquals($expectedResult, $this->channel->nextMediaId());
    }

    public function testingByUserIdIsWorkingFine()
    {
        $user = factory(User::class)->create();
        $this->assertNull(Channel::byUserId($user));

        $expectedChannels = 3;
        factory(Channel::class, $expectedChannels)->create(['user_id' => $user->user_id]);
        $this->assertCount($expectedChannels, Channel::byUserId($user));
    }

    public function testingToPodcastHeaderIsFine()
    {
        $expectedKeys = [
            'title',
            'link',
            'description',
            'coverUrl',
        ];
        $result = $this->channel->toPodcastHeader();
        array_map(function ($key) use ($result) {
            $this->assertArrayHasKey($key, $result, "Converting a channel to a podcast header should have key {$key}.");
        }, $expectedKeys);

        $this->assertEquals($result['explicit'], $this->media->channel->explicit());
    }
}
