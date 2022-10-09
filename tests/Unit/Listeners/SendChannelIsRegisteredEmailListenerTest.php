<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ChannelRegisteredEvent;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Listeners\SendChannelIsRegisteredEmailListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendChannelIsRegisteredEmailListenerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Bus::fake(SendChannelIsRegisteredEmailJob::class);
    }

    /** @test */
    public function listener_should_dispatch_channel_is_registered_email(): void
    {
        $event = new ChannelRegisteredEvent($this->channel);

        $job = new SendChannelIsRegisteredEmailListener();
        $job->handle($event);
        Bus::assertDispatched(SendChannelIsRegisteredEmailJob::class);
        Bus::assertDispatched(
            fn (SendChannelIsRegisteredEmailJob $job) => $job->podcastable->channelId() === $this->channel->youtube_id
        );
    }
}
