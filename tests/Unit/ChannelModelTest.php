<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Plan;
use Carbon\Carbon;
use Tests\TestCase;
use App\Subscription;
use App\Thumb;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ChannelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
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
            config('app.podcasts_url') . "/{$this->channel->channelId()}/" . config('feed_filename'),
            $this->channel->podcastUrl()
        );
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

    
}
