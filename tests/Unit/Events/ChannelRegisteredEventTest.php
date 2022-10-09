<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\ChannelRegisteredEvent;
use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
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
        $this->playlist = Playlist::factory()->create(['channel_id' => $this->channel->channel_id]);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $channelRegisteredEvent = new ChannelRegisteredEvent($this->channel);
        $podcastable = $channelRegisteredEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);

        $channelRegisteredEvent = new ChannelRegisteredEvent($this->playlist);
        $podcastable = $channelRegisteredEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Playlist::class, $podcastable);
    }
}
