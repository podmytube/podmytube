<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ChannelRegisteredEvent;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Jobs\UploadPodcastJob;
use App\Listeners\ChannelIsRegisteredListener;
use App\Models\Channel;
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
        $this->channel = $this->createChannelWithPlan();

        $this->event = new ChannelRegisteredEvent($this->channel);
    }

    /** @test */
    public function uploaded_media_should_dispatch_transfer(): void
    {
        $listener = new ChannelIsRegisteredListener();
        $listener->handle($this->event);

        Bus::assertDispatched(UploadPodcastJob::class, 1);
        Bus::assertDispatched(fn (UploadPodcastJob $job) => $job->podcastable->youtube_id === $this->channel->youtube_id);

        Bus::assertDispatched(SendChannelIsRegisteredEmailJob::class, 1);
        Bus::assertDispatched(fn (SendChannelIsRegisteredEmailJob $job) => $job->podcastable->youtube_id === $this->channel->youtube_id);
    }
}
