<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaModelTest extends TestCase
{
    use RefreshDatabase;

    /** \App\Channel $channel channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testPublishedBetweenShouldBeFine()
    {
        $expectedNbMedias = 5;
        factory(Media::class, $expectedNbMedias)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => Carbon::createFromDate(2019, 12, 15),
        ]);
        $this->assertCount(
            $expectedNbMedias,
            $this->channel
                ->medias()
                ->publishedBetween(
                    Carbon::createFromDate(2019, 12, 1),
                    Carbon::createFromDate(2019, 12, 31)
                )
                ->get()
        );
    }

    public function testPublishedLastMonthShouldBeFine()
    {
        $expectedNbMedias = 3;
        factory(Media::class, $expectedNbMedias)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => Carbon::now()
                ->startOfDay()
                ->subMonth(),
        ]);

        $this->assertCount(
            $expectedNbMedias,
            $this->channel
                ->medias()
                ->publishedLastMonth()
                ->get()
        );
    }

    public function testHasBeenGrabbedShouldBeFine()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => null,
            'published_at' => Carbon::now()
                ->startOfDay()
                ->subMonth(),
        ]);
        $this->assertFalse($media->hasBeenGrabbed());

        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => Carbon::now()
                ->startOfDay()
                ->subMonth(),
        ]);
        $this->assertTrue($media->hasBeenGrabbed());
    }

    public function testMediaExists()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
        $this->assertFalse($media->exists());
    }
}
