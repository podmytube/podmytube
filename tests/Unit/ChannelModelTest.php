<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Plan;
use App\Subscription;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PlansTableSeeder;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => PlansTableSeeder::class]);
        $this->channel = $this->createChannelWithPlan(Plan::find(Plan::FREE_PLAN_ID));
    }

    /** @test */
    public function podcast_url_is_ok(): void
    {
        $this->assertEquals(
            config('app.podcasts_url')."/{$this->channel->channelId()}/".config('app.feed_filename'),
            $this->channel->podcastUrl()
        );
    }

    /** @test */
    public function by_channel_id_is_ok(): void
    {
        $this->assertNull(Channel::byChannelId('this_will_never_exists'));
        $this->assertEquals($this->channel->channel_id, Channel::byChannelId($this->channel->channel_id)->channel_id);
    }

    /** @test */
    public function is_free_should_be_ok(): void
    {
        $this->assertTrue($this->channel->isFree());

        $payingChannel = $this->createChannelWithPlan(Plan::find(Plan::WEEKLY_PLAN_ID));
        $this->assertFalse($payingChannel->isFree());
    }

    public function testing_next_media_id_should_be_ok(): void
    {
        $media = factory(Media::class, 10)->create(['channel_id' => $this->channel->channel_id]);
        $expectedResult = substr(Str::slug($this->channel->channel_name), 0, 20).'-11';
        $this->assertEquals($expectedResult, $this->channel->nextMediaId());
    }

    public function testing_by_user_id_is_working_fine(): void
    {
        $user = factory(User::class)->create();
        $this->assertNull(Channel::byUserId($user));

        $expectedChannels = 3;
        factory(Channel::class, $expectedChannels)->create(['user_id' => $user->user_id]);
        $this->assertCount($expectedChannels, Channel::byUserId($user));
    }

    public function test_relative_feed_path(): void
    {
        $this->assertEquals(
            "{$this->channel->channel_id}/".config('app.feed_filename'),
            $this->channel->relativeFeedPath()
        );
    }

    public function test_remote_file_path(): void
    {
        $this->assertEquals(
            config('app.feed_path')."{$this->channel->channel_id}/".config('app.feed_filename'),
            $this->channel->remoteFilePath()
        );
    }

    /** @test */
    public function should_channel_be_upgraded_is_fine(): void
    {
        // this one is a free one
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

    /** @test */
    public function user_channels_is_ok(): void
    {
        $user = factory(User::class)->create();
        $this->assertCount(0, Channel::userChannels($user));

        $this->channel->update(['user_id' => $user->user_id]);
        $this->assertCount(1, Channel::userChannels($user));

        factory(Channel::class, 5)->create(['user_id' => $user->user_id]);
        $this->assertCount(6, Channel::userChannels($user));
    }

    /** @test */
    public function subscribe_to_plan_should_be_ok(): void
    {
        $channel = factory(Channel::class)->create();
        $this->assertNull($channel->subscription);

        $plan = factory(Plan::class)->create();
        $subscription = $channel->subscribeToPlan($plan);
        $channel->refresh();
        $this->assertNotNull($subscription);
        $this->assertInstanceOf(Subscription::class, $subscription);

        $this->assertNotNull($channel->subscription);
        $this->assertInstanceOf(Subscription::class, $channel->subscription);

        // checking plan subscription
        $this->assertNotNull($channel->subscription->plan);
        $this->assertInstanceOf(Plan::class, $channel->subscription->plan);
        $this->assertEquals($plan->name, $channel->subscription->plan->name);
    }

    /** @test */
    public function all_active_channels_should_be_empty(): void
    {
        $this->channel->update(['active' => false]);
        $results = Channel::allActiveChannels();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }

    /** @test */
    public function all_active_channels_should_be_fine(): void
    {
        /** faking uploaded file */
        $uploadedFile = UploadedFile::fake()->image('photo1.jpg');

        // adding cover to channel
        $this->channel->setCoverFromUploadedFile($uploadedFile);

        $inactiveChannel = factory(Channel::class)->create(['active' => false]);
        $results = Channel::allActiveChannels();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->hasCover());

        $onlyChannelIds = $results->pluck('channel_id')->toArray();
        $this->assertContains($this->channel->channel_id, $onlyChannelIds);
        $this->assertNotContains($inactiveChannel, $onlyChannelIds);
    }

    /** @test */
    public function associated_medias_is_fine(): void
    {
        /** no medias */
        $medias = $this->channel->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(0, $medias);

        // with active medias
        factory(Media::class, 3)->create(['channel_id' => $this->channel->channelId(), 'active' => true]);
        $this->channel->refresh();

        $medias = $this->channel->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(3, $medias);

        // with inactive medias
        factory(Media::class, 2)->create(['channel_id' => $this->channel->channelId(), 'active' => false]);
        $this->channel->refresh();

        $medias = $this->channel->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(5, $medias);
    }
}
