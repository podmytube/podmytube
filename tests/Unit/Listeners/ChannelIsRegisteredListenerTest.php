<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ChannelRegisteredEvent;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Jobs\SendNewReferralEmailJob;
use App\Jobs\UploadPodcastJob;
use App\Listeners\ChannelIsRegisteredListener;
use App\Models\Channel;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelIsRegisteredListenerTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected ChannelRegisteredEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake();
    }

    /** @test */
    public function registered_channel_with_no_referrer_should_dispatch_transfer(): void
    {
        $channel = $this->createChannelWithPlan();
        $event = new ChannelRegisteredEvent($channel);

        $listener = new ChannelIsRegisteredListener();
        $listener->handle($event);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $channel->youtube_id);

        Bus::assertDispatched(SendChannelIsRegisteredEmailJob::class, 1);
        Bus::assertDispatched(fn (SendChannelIsRegisteredEmailJob $job) => $job->podcastable->youtube_id === $channel->youtube_id);

        Bus::assertNotDispatched(SendNewReferralEmailJob::class);
    }

    /** @test */
    public function registered_channel_with_one_referrer_should_dispatch_transfer(): void
    {
        $starterPlan = Plan::factory()->name('starter')->create();
        $referrer = User::factory()->verifiedAt(now()->subMonth())->create();
        $referral = User::factory()->verifiedAt(now())->withReferrer($referrer)->create();
        $channel = $this->createChannel($referral, $starterPlan);
        $event = new ChannelRegisteredEvent($channel);

        $listener = new ChannelIsRegisteredListener();
        $listener->handle($event);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $channel->youtube_id);

        Bus::assertDispatched(SendChannelIsRegisteredEmailJob::class, 1);
        Bus::assertDispatched(fn (SendChannelIsRegisteredEmailJob $job) => $job->podcastable->youtube_id === $channel->youtube_id);

        Bus::assertDispatched(SendNewReferralEmailJob::class, 1);
        Bus::assertDispatched(fn (SendNewReferralEmailJob $job) => $job->channel->youtube_id === $channel->youtube_id);
    }
}
