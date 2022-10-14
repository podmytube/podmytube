<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Playlist;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelModelTest extends TestCase
{
    use Covers;
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $freePlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();

        $this->freePlan = Plan::factory()->isFree()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->channel = $this->createChannelWithPlan($this->freePlan);
    }

    /** @test */
    public function podcast_url_is_ok(): void
    {
        $this->assertEquals(
            config('app.podcasts_url') . "/{$this->channel->channelId()}/" . config('app.feed_filename'),
            $this->channel->podcastUrl()
        );
    }

    /** @test */
    public function by_channel_id_is_ok(): void
    {
        $this->assertNull(Channel::byChannelId('this_will_never_exists'));

        $result = Channel::byChannelId($this->channel->channel_id);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Channel::class, $result);
        $this->assertEquals($this->channel->channel_id, $result->channel_id);
    }

    /** @test */
    public function is_free_should_be_ok(): void
    {
        $this->assertTrue($this->channel->isFree());

        $payingChannel = $this->createChannelWithPlan($this->starterPlan);
        $this->assertFalse($payingChannel->isFree());
    }

    /** @test */
    public function next_media_id_should_be_ok(): void
    {
        $this->assertStringStartsWith($this->channel->slugChannelName(), $this->channel->nextMediaId());
    }

    /** @test */
    public function by_user_id_is_working_fine(): void
    {
        $user = User::factory()->create();
        $this->assertNull(Channel::byUserId($user));

        $expectedChannels = 3;
        Channel::factory()->count($expectedChannels)->create(['user_id' => $user->user_id]);
        $this->assertCount($expectedChannels, Channel::byUserId($user));
    }

    /** @test */
    public function relative_feed_path_is_fine(): void
    {
        $this->assertEquals(
            "{$this->channel->channel_id}/" . config('app.feed_filename'),
            $this->channel->relativeFeedPath()
        );
    }

    /** @test */
    public function remote_file_path_is_fine(): void
    {
        $this->assertEquals(
            config('app.feed_path') . "{$this->channel->channel_id}/" . config('app.feed_filename'),
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
        Media::factory()
            ->grabbedAt(now()->subHour())
            ->count(10)
            ->create(
                [
                    'channel_id' => $channelWhichIsNotPayingEnough->channel_id,
                ]
            )
        ;
        $this->assertTrue($channelWhichIsNotPayingEnough->shouldChannelBeUpgraded());
    }

    /** @test */
    public function user_channels_is_ok(): void
    {
        $user = User::factory()->create();
        $this->assertCount(0, Channel::userChannels($user));

        $this->channel->update(['user_id' => $user->user_id]);
        $this->assertCount(1, Channel::userChannels($user));

        Channel::factory()->count(5)->create(['user_id' => $user->user_id]);
        $this->assertCount(6, Channel::userChannels($user));
    }

    /** @test */
    public function subscribe_to_plan_should_be_ok(): void
    {
        $channel = Channel::factory()->create();
        $this->assertNull($channel->subscription);

        $plan = Plan::factory()->create();
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
    public function with_no_active_channel_all_active_channels_should_be_empty(): void
    {
        $this->channel->update(['active' => 0]);
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

        $inactiveChannel = Channel::factory()->create(['active' => false]);
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
        Media::factory()
            ->count(3)
            ->create(['channel_id' => $this->channel->channelId(), 'active' => true])
        ;
        $this->channel->refresh();

        $medias = $this->channel->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(3, $medias);

        // with inactive medias
        Media::factory()
            ->count(2)
            ->create(['channel_id' => $this->channel->channelId(), 'active' => false])
        ;
        $this->channel->refresh();

        $medias = $this->channel->associatedMedias();
        $this->assertNotNull($medias);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(5, $medias);
    }

    /** @test */
    public function slug_name_is_fine(): void
    {
        $expectedSlugChannelName = substr(Str::slug($this->channel->channel_name), 0, 20);
        $this->assertEquals($expectedSlugChannelName, $this->channel->slugChannelName());
    }

    /** @test */
    public function is_paying_channel_is_fine(): void
    {
        $channel = $this->createChannelWithPlan($this->freePlan);
        $this->assertFalse($channel->isPaying());

        $channel = $this->createChannelWithPlan($this->starterPlan);
        $this->assertTrue($channel->isPaying());
    }

    /** @test */
    public function youtube_url_is_fine(): void
    {
        $expectedChannelYoutubeUrl = 'https://www.youtube.com/channel/' . $this->channel->youtube_id;
        $this->assertEquals($expectedChannelYoutubeUrl, $this->channel->youtubeUrl());
    }

    /** @test */
    public function has_subscription_is_fine(): void
    {
        $channelWithoutSubscription = Channel::factory()->create();
        $this->assertFalse($channelWithoutSubscription->hasSubscription());

        $this->assertTrue($this->channel->hasSubscription());
    }

    /** @test */
    public function has_recently_added_medias_is_fine(): void
    {
        // no medias should return false
        $channel = $this->createChannelWithPlan($this->starterPlan);
        $this->assertFalse($channel->hasRecentlyAddedMedias());

        // with last day created medias should return false
        Media::factory()->create(['channel_id' => $channel->channel_id, 'created_at' => now()->subDay()]);
        $this->assertFalse($channel->hasRecentlyAddedMedias());

        // 1h is no recent media should return false
        Media::factory()->create(['channel_id' => $channel->channel_id, 'created_at' => now()->subHour()]);
        $this->assertFalse($channel->hasRecentlyAddedMedias());

        // recent medias should return true
        Media::factory()->create(['channel_id' => $channel->channel_id, 'created_at' => now()->subMinutes(1)]);
        $this->assertTrue($channel->hasRecentlyAddedMedias());
    }

    /** @test */
    public function should_update_podcast_updated_at(): void
    {
        $this->channel->update(['podcast_updatedAt' => null]);
        $this->assertNull($this->channel->podcast_updatedAt);

        $now = now();
        $this->channel->wasUpdatedOn($now);
        $this->assertEquals($now->toDateString(), $this->channel->podcast_updatedAt->toDateString());
    }

    /** @test */
    public function relative_folder_path_should_be_good(): void
    {
        $this->assertEquals($this->channel->channel_id, $this->channel->relativeFolderPath());
    }

    public function feed_folder_path_should_be_good(): void
    {
        $this->assertEquals(
            config('app.feed_path') . $this->channel->relativeFolderPath(),
            $this->channel->feedFolderPath()
        );
    }

    /** @test */
    public function mp3_folder_path_is_fine(): void
    {
        $this->assertEquals(
            config('app.mp3_path') . $this->channel->relativeFolderPath(),
            $this->channel->mp3FolderPath()
        );
    }

    /** @test */
    public function playlists_folder_path_is_fine(): void
    {
        $this->assertEquals(
            config('app.playlists_path') . $this->channel->relativeFolderPath(),
            $this->channel->playlistFolderPath()
        );
    }

    /** @test */
    public function youtube_id_attribute_is_fine(): void
    {
        $this->assertNotNull($this->channel->youtube_id);
        $this->assertEquals($this->channel->channel_id, $this->channel->youtube_id);
    }

    /** @test */
    public function user_channels_optimized(): void
    {
        // one user
        /** @var User $user */
        $user = User::factory()->verifiedAt(now())->create();

        // with 2 channels
        $freeChannel = $this->createChannel($user, $this->freePlan);

        // one is free other is starter
        $paidChannel = $this->createChannel($user, $this->starterPlan);

        // creating a collection for these channels
        $channels = collect([])->push($freeChannel, $paidChannel);

        // the paid one has one playlist
        $playlist = Playlist::factory()->channel($paidChannel)->create();

        // all channels have thumb/cover and vignettes
        $channels->each(function (Channel $channel): void {
            $this->createCoverFor($channel);
        });

        // playlist has cover too
        $this->createCoverFor($playlist);

        $results = Channel::query()
            ->select('user_id', 'channel_id', 'channel_name', 'podcast_title', 'active')
            ->where('user_id', '=', $user->id())
            ->with([
                'playlists:channel_id,active',
                'subscription:channel_id,plan_id',
                'subscription.plan:id,name',
            ])
            ->get()
        ;

        $results->contains(function(Channel $freeChannel){
            return $freeChannel->youtubeId
        }/* 'channel_id', $channel->youtube_id */);

        // for each channel
        $channels->each(function (Channel $channel) use ($results): void {
            // check results is containing youtube_id
            $this->assertTrue($results->contains('channel_id', $channel->youtube_id));
            $this->assertTrue($results->contains('channel_name', $channel->channel_name));

        });
    }
}
