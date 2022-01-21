<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Channel;
use App\Events\ChannelRegistered;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelRegisteredEventTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = factory(Playlist::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    /** @test */
    public function class_is_instanciable(): void
    {
        $channelRegisteredEvent = new ChannelRegistered($this->channel);
        $this->assertNotNull($channelRegisteredEvent);
        $this->assertInstanceOf(ChannelRegistered::class, $channelRegisteredEvent);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $channelRegisteredEvent = new ChannelRegistered($this->channel);
        $podcastable = $channelRegisteredEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);

        $channelRegisteredEvent = new ChannelRegistered($this->playlist);
        $podcastable = $channelRegisteredEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Playlist::class, $podcastable);
    }
}
