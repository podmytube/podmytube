<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\ThumbUpdated;
use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ThumbUpdatedEventTest extends TestCase
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
    public function class_is_instanciable(): void
    {
        $thumbUpdatedEvent = new ThumbUpdated($this->channel);
        $this->assertNotNull($thumbUpdatedEvent);
        $this->assertInstanceOf(ThumbUpdated::class, $thumbUpdatedEvent);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $thumbUpdatedEvent = new ThumbUpdated($this->channel);
        $podcastable = $thumbUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);

        $thumbUpdatedEvent = new ThumbUpdated($this->playlist);
        $podcastable = $thumbUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Playlist::class, $podcastable);
    }
}
