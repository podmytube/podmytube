<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Channel;
use App\Events\ThumbUpdated;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ThumbUpdatedEventTest extends TestCase
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
