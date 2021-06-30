<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MediaModelTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create(['explicit' => false]);
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);
    }

    public function testPublishedBetweenShouldBeFine(): void
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

    public function testPublishedLastMonthShouldBeFine(): void
    {
        $expectedNbMedias = 3;
        factory(Media::class, $expectedNbMedias)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => null,
            'published_at' => now()->startOfMonth()->subMonth(),
        ]);

        $this->assertCount(
            $expectedNbMedias,
            $this->channel
                ->medias()
                ->publishedLastMonth()
                ->get()
        );
    }

    /** @test */
    public function isGrabbedIsOk(): void
    {
        $this->media->update(['grabbed_at' => null]);
        $this->assertFalse($this->media->isGrabbed());

        $this->media->update(['grabbed_at' => now()]);
        //$this->media->refresh();
        $this->assertTrue($this->media->isGrabbed());
    }

    public function testGrabbedAtShouldBeFine(): void
    {
        $expectedResult = 3;
        factory(Media::class, $expectedResult)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);

        $this->assertEquals($expectedResult, Media::grabbedAt()->count());
    }

    public function testingByMediaIdShouldBeGood(): void
    {
        $this->assertEquals($this->media->title, Media::byMediaId($this->media->media_id)->title);
        $this->assertNull(Media::byMediaId('ThisIsNotAMediaId'));
    }

    public function testMediaFileName(): void
    {
        $expectedMediaFileName = $this->media->media_id.Media::FILE_EXTENSION;
        $this->assertEquals(
            $expectedMediaFileName,
            $result = $this->media->mediaFileName(),
            "Expected media filename was {$expectedMediaFileName}, obtained {$result}"
        );
    }

    public function testUploadedPath(): void
    {
        $expectedFilePath = Storage::disk(Media::UPLOADED_BY_USER_DISK)
            ->path($this->media->mediaFileName())
        ;

        $this->assertEquals(
            $expectedFilePath,
            $this->media->uploadedFilePath()
        );
    }

    public function testMediaFilenameIsOk(): void
    {
        $this->assertEquals(
            $this->media->media_id.'.mp3',
            $this->media->mediaFileName()
        );
    }

    public function testRelativePathIsOk(): void
    {
        $this->assertEquals(
            $this->media->channel->channel_id.'/'.$this->media->mediaFileName(),
            $this->media->relativePath()
        );
    }

    public function testRemotePathIsOk(): void
    {
        $this->assertEquals(
            config('app.mp3_path').$this->media->relativePath(),
            $this->media->remoteFilePath()
        );
    }

    public function testToPodcastItemShouldReturnEveryField(): void
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
        array_map(function ($key) use ($result): void {
            $this->assertArrayHasKey($key, $result, "Converting a media to a podcast item should have key {$key}.");
        }, $expectedKeys);
    }

    public function testToPodcastItemWithNonExplicitChannel(): void
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

    public function testToPodcastItemWithExplicitChannel(): void
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

    public function testToPodcastItemWithEmptyMediaInfos(): void
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
        array_map(function ($key) use ($result): void {
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

    /** @test */
    public function youtubeWatchUrlIsOk(): void
    {
        $this->assertEquals(
            "https://www.youtube.com/watch?v={$this->media->media_id}",
            $this->media->youtubeWatchUrl()
        );
    }

    /** @test */
    public function publishedAtIsFine(): void
    {
        $this->media->update(['published_at' => null]);
        $result = $this->media->publishedAt();
        $this->assertNotNull($result);
        $this->assertIsString($result);
        $this->assertEquals('---', $result);

        $publishedAt = now();
        $this->media->update(['published_at' => $publishedAt]);
        $result = $this->media->publishedAt();
        $this->assertNotNull($result);
        $this->assertIsString($result);
        $this->assertEquals($publishedAt->format('Y-m-d'), $result);
    }
}
