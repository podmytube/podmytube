<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class MediaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    public function testPublishedBetweenShouldBeFine()
    {
        $expectedNbMedias = 3;
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
        $this->media->grabbed_at = Carbon::now();
        $this->media->save();
        $this->media->refresh();
        $this->assertTrue($this->media->hasBeenGrabbed());
    }

    public function testGrabbedAtShouldBeFine()
    {
        $expectedResult = 3;
        factory(Media::class, $expectedResult)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);

        $this->assertEquals($expectedResult, Media::grabbedAt()->count());
    }

    public function testingByMediaIdShouldBeGood()
    {
        $this->assertEquals($this->media->title, Media::byMediaId($this->media->media_id)->title);
        $this->assertNull(Media::byMediaId('ThisIsNotAMediaId'));
    }

    public function testMediaFileName()
    {
        $expectedMediaFileName = $this->media->media_id . Media::FILE_EXTENSION;
        $this->assertEquals(
            $expectedMediaFileName,
            $result = $this->media->mediaFileName(),
            "Expected media filename was {$expectedMediaFileName}, obtained {$result}"
        );
    }

    public function testUploadedPath()
    {
        $expectedFilePath = Storage::disk(Media::UPLOADED_BY_USER_DISK)
            ->path($this->media->mediaFileName());

        $this->assertEquals(
            $expectedFilePath,
            $this->media->uploadedFilePath()
        );
    }

    public function testRemotePath()
    {
        $expectedFilePath = config('app.mp3_path') . $this->media->relativePath();
        $this->assertEquals(
            $expectedFilePath,
            $this->media->remoteFilePath()
        );
    }
}
