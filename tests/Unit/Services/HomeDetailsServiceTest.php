<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Playlist;
use App\Models\Subscription;
use App\Models\Thumb;
use App\Models\User;
use App\Services\HomeDetailsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Covers;
use Tests\Traits\Downloads;

/**
 * @internal
 *
 * @coversNothing
 */
class HomeDetailsServiceTest extends TestCase
{
    use Covers;
    use Downloads;
    use RefreshDatabase;

    protected HomeDetailsService $service;
    protected Plan $freePlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new HomeDetailsService();
        $this->freePlan = Plan::factory()->isFree()->create();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function user_channels_should_be_good(): void
    {
        // ===============================================================
        // preparing
        // ===============================================================

        // one user
        /** @var User $user */
        $user = User::factory()->verifiedAt(now())->create();

        // with 2 channels
        // one is free other is starter
        $freeChannel = $this->createChannel($user, $this->freePlan);
        $paidChannel = $this->createChannel($user, $this->starterPlan);

        // $paidchannel has cover/thumb
        $paidChannelCover = Thumb::factory()->create();
        $paidChannel->attachCover($paidChannelCover);

        // creating a collection for these channels
        $channels = collect([])->push($freeChannel, $paidChannel);

        // the paid one has one playlist
        $playlist = Playlist::factory()->channel($paidChannel)->create();

        $channels->each(function (Channel $channel): void {
            // all channels have thumb/cover and vignettes
            $this->createCoverFor($channel);

            // all channels have medias (1-5)
            Media::factory(fake()->numberBetween(1, 5))
                ->channel($channel)
                ->create()
            ;

            // all channel medias have downloads
            $downloadsForChannel = $this->addDownloadsForChannelMediasDuringPeriod($channel, now()->subMonth(), now());
        });

        // playlist has cover too
        $this->createCoverFor($playlist);

        // ===============================================================
        $results = $this->service->userContent($user);
        // ===============================================================

        // should have 2 channels in results
        $this->assertCount($channels->count(), $results);

        // both channels should be present in results
        $channels->each(function (Channel $channel) use ($results): void {
            /** @var Channel $result */
            $result = $results->where('channel_id', '=', $channel->youtube_id)->first();
            $this->assertNotNull($result);
            $this->assertInstanceOf(Channel::class, $result);
            // channel name
            $this->assertEquals($channel->channel_name, $result->channel_name);
            // podcast title
            $this->assertEquals($channel->title(), $result->title());

            // subscription and plans
            $this->assertNotNull($result->subscription);
            $this->assertInstanceOf(Subscription::class, $result->subscription);
            $this->assertNotNull($result->subscription->plan);
            $this->assertInstanceOf(Plan::class, $result->subscription->plan);
            $this->assertEquals($channel->subscription->plan->name, $result->subscription->plan->name);
            if ($channel->subscription->plan->id === $this->freePlan->id) {
                // freePlan
                $this->assertEquals('forever free', $result->subscription->plan->name);
                $this->assertEquals(0, $result->subscription->plan->price);
                $this->assertTrue($result->isFree());
            } else {
                // paying plan
                $this->assertEquals('starter', $result->subscription->plan->name);
                $this->assertGreaterThan(0, $result->subscription->plan->price);
                $this->assertFalse($result->isFree());
            }

            // vignette url is fine
            $this->assertNotNull($result->vignette_url, 'vignette_url should be one url.');
            $this->assertEquals($channel->vignette_url, $result->vignette_url);
            // cover url is fine
            $this->assertNotNull($result->cover_url, 'cover_url should be one url.');
            $this->assertEquals($channel->cover_url, $result->cover_url);

            // downloads TODO
            // some results should be cached (last week/last month/last past one)
        });
    }
}
