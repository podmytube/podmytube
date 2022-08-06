<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Channel;
use App\Events\PodcastUpdated;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PodcastUpdatedEventTest extends TestCase
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
        $podcastUpdatedEvent = new PodcastUpdated($this->channel);
        $this->assertNotNull($podcastUpdatedEvent);
        $this->assertInstanceOf(PodcastUpdated::class, $podcastUpdatedEvent);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $podcastUpdatedEvent = new PodcastUpdated($this->channel);
        $podcastable = $podcastUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);

        $podcastUpdatedEvent = new PodcastUpdated($this->playlist);
        $podcastable = $podcastUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Playlist::class, $podcastable);
    }
}
