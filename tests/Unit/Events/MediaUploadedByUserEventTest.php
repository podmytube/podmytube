<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\MediaUploadedByUserEvent;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MediaUploadedByUserEventTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    protected Playlist $playlist;

    protected Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = Playlist::factory()->create(['channel_id' => $this->channel->channel_id]);
        $this->media = Media::factory()->create(['channel_id' => $this->channel->channel_id]);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $thumbUpdatedEvent = new MediaUploadedByUserEvent($this->media);
        $podcastable = $thumbUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);
        $this->assertEquals($this->channel->youtube_id, $podcastable->youtube_id);
    }

    /** @test */
    public function media_is_fine(): void
    {
        $thumbUpdatedEvent = new MediaUploadedByUserEvent($this->media);
        $media = $thumbUpdatedEvent->media();
        $this->assertNotNull($media);
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals($this->media->youtube_id, $media->youtube_id);
    }
}
