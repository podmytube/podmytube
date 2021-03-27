<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Plan;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PlansTableSeeder;

class ChannelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => PlansTableSeeder::class]);
        $this->channel = $this->createChannelWithPlan(Plan::find(Plan::FREE_PLAN_ID));
    }

    /** @test */
    public function podcast_url_is_ok()
    {
        $this->assertEquals(
            config('app.podcasts_url') . "/{$this->channel->channelId()}/" . config('app.feed_filename'),
            $this->channel->podcastUrl()
        );
    }

    /** @test */
    public function by_channel_id_is_ok()
    {
        $this->assertNull(Channel::byChannelId('this_will_never_exists'));
        $this->assertEquals($this->channel->channel_id, Channel::byChannelId($this->channel->channel_id)->channel_id);
    }

    /** @test */
    public function is_free_should_be_ok()
    {
        $this->assertTrue($this->channel->isFree());

        $payingChannel = $this->createChannelWithPlan(Plan::find(Plan::WEEKLY_PLAN_ID));
        $this->assertFalse($payingChannel->isFree());
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

    public function testRelativeFeedPath()
    {
        $this->assertEquals(
            "{$this->channel->channel_id}/" . config('app.feed_filename'),
            $this->channel->relativeFeedPath()
        );
    }

    public function testRemoteFilePath()
    {
        $this->assertEquals(
            config('app.feed_path') . "{$this->channel->channel_id}/" . config('app.feed_filename'),
            $this->channel->remoteFilePath()
        );
    }

    /** @test */
    public function should_channel_be_upgraded_is_fine()
    {
        /** this one is a free one */
        $this->assertTrue($this->channel->shouldChannelBeUpgraded());

        /** with a paying one */
        $channelWithEnoughQuota = $this->createChannelWithPlan(Plan::bySlug('weekly_youtuber'));
        $this->addMediasToChannel($channelWithEnoughQuota, 2, true);
        $this->assertFalse($channelWithEnoughQuota->shouldChannelBeUpgraded());

        /** with not paying enough channel */
        $channelWhichIsNotPayingEnough = $this->createChannelWithPlan(Plan::bySlug('weekly_youtuber'));
        factory(Media::class, 10)->create(
            [
                'channel_id' => $channelWhichIsNotPayingEnough->channel_id,
                'grabbed_at' => now()->subHour(),
            ]
        );
        $this->assertTrue($channelWhichIsNotPayingEnough->shouldChannelBeUpgraded());
    }
}
