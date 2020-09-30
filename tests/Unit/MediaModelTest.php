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
            'grabbed_at' => null,
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

    public function testHasBeenGrabbedShouldBeFalse()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => null,
            'published_at' => Carbon::now()
                ->startOfDay()
                ->subMonth(),
        ]);
        $this->assertFalse($media->hasBeenGrabbed());
    }

    public function testHasBeenGrabbedShouldBeTrue()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'published_at' => Carbon::now()
                ->startOfDay()
                ->subMonth(),
        ]);
        $this->assertTrue($media->hasBeenGrabbed());
    }

    public function testMediaFileExistsShouldBeFalse()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
        $this->assertFalse($media->fileExists());
    }

    public function testGrabbedAtShouldBeFine()
    {
        if (Media::grabbedAt()->count()) {
            /**
             * I don't know why, on this specific test,
             * db may be not empty (as expected)
             * so I truncated it to avoid false positive.
             */
            Media::truncate();
        }
        $expectedResult = 20;
        factory(Media::class, $expectedResult)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);

        $this->assertEquals($expectedResult, Media::grabbedAt()->count());
    }

    public function testingByMediaIdShouldBeGood()
    {
        /** preparation */
        $media = factory(Media::class)->create();

        /** checking results */
        $this->assertEquals($media->title, Media::byMediaId($media->media_id)->title);
        $this->assertNull(Media::byMediaId('ThisIsNotAMediaId'));
    }
}
