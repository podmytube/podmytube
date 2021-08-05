<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Channel;
use App\Events\MediaUploadedByUser;
use App\Media;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MediaUploadedByUserEventTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Playlist */
    protected $playlist;

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = factory(Playlist::class)->create(['channel_id' => $this->channel->channel_id]);
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    /** @test */
    public function class_is_instanciable(): void
    {
        $thumbUpdatedEvent = new MediaUploadedByUser($this->media);
        $this->assertNotNull($thumbUpdatedEvent);
        $this->assertInstanceOf(MediaUploadedByUser::class, $thumbUpdatedEvent);
    }

    /** @test */
    public function podcastable_is_fine(): void
    {
        $thumbUpdatedEvent = new MediaUploadedByUser($this->media);
        $podcastable = $thumbUpdatedEvent->podcastable();
        $this->assertNotNull($podcastable);
        $this->assertInstanceOf(Channel::class, $podcastable);
        $this->assertEquals($this->channel->channel_id, $podcastable->channel_id);
    }

    /** @test */
    public function media_is_fine(): void
    {
        $thumbUpdatedEvent = new MediaUploadedByUser($this->media);
        $media = $thumbUpdatedEvent->media();
        $this->assertNotNull($media);
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals($this->media->media_id, $media->media_id);
    }
}
