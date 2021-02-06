<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Storage;
use Tests\TestCase;

class MediaModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create(['explicit' => false]);
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

    public function testMediaFilenameIsOk()
    {
        $this->assertEquals(
            $this->media->media_id . '.mp3',
            $this->media->mediaFileName()
        );
    }

    public function testRelativePathIsOk()
    {
        $this->assertEquals(
            $this->media->channel->channel_id . '/' . $this->media->mediaFileName(),
            $this->media->relativePath()
        );
    }

    public function testRemotePathIsOk()
    {
        $this->assertEquals(
            config('app.mp3_path') . $this->media->relativePath(),
            $this->media->remoteFilePath()
        );
    }

    public function testToPodcastItemShouldReturnEveryField()
    {
        $expectedKeys = [
            'guid',
            'title',
            'enclosureUrl',
            'mediaLength',
            'pubDate',
            'description',
            'duration',
            'explicit',
        ];
        $result = $this->media->toPodcastItem();
        array_map(function ($key) use ($result) {
            $this->assertArrayHasKey($key, $result, "Converting a media to a podcast item should have key {$key}.");
        }, $expectedKeys);
    }

    public function testToPodcastItemWithNonExplicitChannel()
    {
        $result = $this->media->toPodcastItem();
        $this->assertEquals($result['guid'], $this->media->media_id);
        $this->assertEquals($result['title'], $this->media->title);
        $this->assertEquals($result['enclosureUrl'], $this->media->enclosureUrl());
        $this->assertEquals($result['mediaLength'], $this->media->length);
        $this->assertEquals($result['pubDate'], $this->media->pubDate());
        $this->assertEquals($result['description'], $this->media->description);
        $this->assertEquals($result['duration'], $this->media->duration());
        $this->assertEquals($result['explicit'], 'false');
    }

    public function testToPodcastItemWithExplicitChannel()
    {
        $channel = factory(Channel::class)->create(['explicit' => true]);
        $media = factory(Media::class)->create(['channel_id' => $channel->channel_id]);
        $result = $media->toPodcastItem();
        $this->assertEquals($result['guid'], $media->media_id);
        $this->assertEquals($result['title'], $media->title);
        $this->assertEquals($result['enclosureUrl'], $media->enclosureUrl());
        $this->assertEquals($result['mediaLength'], $media->length);
        $this->assertEquals($result['pubDate'], $media->pubDate());
        $this->assertEquals($result['description'], $media->description);
        $this->assertEquals($result['duration'], $media->duration());
        $this->assertEquals($result['explicit'], 'true');
    }

    public function testToPodcastItemWithEmptyMediaInfos()
    {
        $expectedKeys = [
            'guid',
            'title',
            'enclosureUrl',
            'mediaLength',
            'pubDate',
            'description',
            'duration',
            'explicit',
        ];
        $media = factory(Media::class)->create([
            'media_id' => $this->faker->regexify('[a-zA-Z0-9-_]{8}'),
            'channel_id' => $this->channel->channel_id,
            'title' => null,
            'description' => null,
            'length' => 0,
            'duration' => 0,
            'published_at' => $this->faker->dateTimeBetween(Carbon::now()->startOfMonth(), Carbon::now()),
            'grabbed_at' => null,
            'active' => true,
        ]);
        $result = $media->toPodcastItem();
        array_map(function ($key) use ($result) {
            $this->assertArrayHasKey($key, $result, "Converting a media to a podcast item should have key {$key}.");
        }, $expectedKeys);

        $this->assertEquals($result['guid'], $media->media_id);
        $this->assertNull($result['title'], 'title should be null');
        $this->assertNull($result['description'], 'description should be null');
        $this->assertEquals($result['enclosureUrl'], $media->enclosureUrl());
        $this->assertEquals($result['mediaLength'], $media->length);
        $this->assertEquals($result['pubDate'], $media->pubDate());
        $this->assertEquals($result['duration'], $media->duration());
        $this->assertEquals($result['explicit'], 'false');
    }
}
