<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Channel;
use App\Events\ChannelUpdated;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelUpdatedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = factory(Playlist::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    /** @test */
    public function class_is_instanciable(): void
    {
        $channelUpdatedEvent = new ChannelUpdated($this->channel);
        $this->assertNotNull($channelUpdatedEvent);
        $this->assertInstanceOf(ChannelUpdated::class, $channelUpdatedEvent);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $channelUpdatedEvent = new ChannelUpdated($this->channel);
        $podcastable = $channelUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);

        $channelUpdatedEvent = new ChannelUpdated($this->playlist);
        $podcastable = $channelUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Playlist::class, $podcastable);
    }
}
