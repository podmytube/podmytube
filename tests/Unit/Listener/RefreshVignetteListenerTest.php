<?php

namespace Tests\Unit\Listeners;

use App\Events\ThumbUpdated;
use App\Jobs\CreateVignetteFromThumb;
use App\Listeners\RefreshVignette;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RefreshVignetteListenerTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        factory(Thumb::class)->create(['channel_id' => $this->channel->channelId()]);
        Bus::fake();
    }

    public function testUploadPodcastListenerForChannel()
    {
        $thumbUpdatedEvent = new ThumbUpdated($this->channel);
        $this->assertTrue((new RefreshVignette)->handle($thumbUpdatedEvent));
        Bus::assertDispatched(function (CreateVignetteFromThumb $job) {
            return true;
        });
    }
}
