<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Jobs\SendFileBySFTP;
use App\Media;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

    public function test_published_between_should_be_fine(): void
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

    public function test_published_last_month_should_be_fine(): void
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
    public function is_grabbed_is_ok(): void
    {
        $this->media->update(['grabbed_at' => null]);
        $this->assertFalse($this->media->isGrabbed());

        $this->media->update(['grabbed_at' => now()]);
        //$this->media->refresh();
        $this->assertTrue($this->media->isGrabbed());
    }

    public function test_grabbed_at_should_be_fine(): void
    {
        $expectedResult = 3;
        factory(Media::class, $expectedResult)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => Carbon::now(),
        ]);

        $this->assertEquals($expectedResult, Media::grabbedAt()->count());
    }

    /** @test */
    public function by_media_id_is_fine(): void
    {
        $this->assertNull(Media::byMediaId('ThisIsNotAMediaId'));
        $this->assertEquals($this->media->title, Media::byMediaId($this->media->media_id)->title);

        /** same with deleted media */
        $deletedMedia = factory(Media::class)->create(['deleted_at' => now()]);
        // that should not be found here
        $this->assertNull(Media::byMediaId($deletedMedia->media_id));

        /** and should be found here */
        $foundMedia = Media::byMediaId($deletedMedia->media_id, true);
        $this->assertNotNull($foundMedia);
        $this->assertInstanceOf(Media::class, $foundMedia);
        $this->assertEquals($deletedMedia->title, $foundMedia->title);
    }

    public function test_media_file_name(): void
    {
        $expectedMediaFileName = $this->media->media_id . Media::FILE_EXTENSION;
        $this->assertEquals(
            $expectedMediaFileName,
            $result = $this->media->mediaFileName(),
            "Expected media filename was {$expectedMediaFileName}, obtained {$result}"
        );
    }

    public function test_uploaded_path(): void
    {
        $expectedFilePath = Storage::disk(Media::UPLOADED_BY_USER_DISK)
            ->path($this->media->mediaFileName())
        ;

        $this->assertEquals(
            $expectedFilePath,
            $this->media->uploadedFilePath()
        );
    }

    public function test_media_filename_is_ok(): void
    {
        $this->assertEquals(
            $this->media->media_id . '.mp3',
            $this->media->mediaFileName()
        );
    }

    public function test_relative_path_is_ok(): void
    {
        $this->assertEquals(
            $this->media->channel->channel_id . '/' . $this->media->mediaFileName(),
            $this->media->relativePath()
        );
    }

    public function test_remote_path_is_ok(): void
    {
        $this->assertEquals(
            config('app.mp3_path') . $this->media->relativePath(),
            $this->media->remoteFilePath()
        );
    }

    public function test_to_podcast_item_should_return_every_field(): void
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

    public function test_to_podcast_item_with_non_explicit_channel(): void
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

    public function test_to_podcast_item_with_explicit_channel(): void
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

    public function test_to_podcast_item_with_empty_media_infos(): void
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
    public function youtube_watch_url_is_ok(): void
    {
        $this->assertEquals(
            "https://www.youtube.com/watch?v={$this->media->media_id}",
            $this->media->youtubeWatchUrl()
        );
    }

    /** @test */
    public function published_at_is_fine(): void
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

    /** @test */
    public function is_uploaded_by_user_is_fine(): void
    {
        $media = factory(Media::class)->create(['uploaded_by_user' => false]);
        $this->assertFalse($media->isUploadedByUser());

        $media = factory(Media::class)->create(['uploaded_by_user' => true]);
        $this->assertTrue($media->isUploadedByUser());
    }

    /** @test */
    public function remote_file_exists_is_ok(): void
    {
        Storage::fake(SendFileBySFTP::REMOTE_DISK);

        $this->assertFalse($this->media->remoteFileExists());

        Storage::disk(SendFileBySFTP::REMOTE_DISK)->put(
            $this->media->remoteFilePath(),
            file_get_contents(base_path('tests/Fixtures/Audio/l8i4O7_btaA.mp3'))
        );

        $this->assertTrue($this->media->remoteFileExists());
    }

    /** @test */
    public function ungrabbed_media_is_fine_by_default(): void
    {
        // should be empty
        $someChannel = $this->createChannelWithPlan();
        $medias = Media::ungrabbedMediasForChannel($someChannel);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount(0, $medias);

        // should have some episode
        $expectedNumberOfMediasForSomeChannel = 2;
        $this->addMediasToChannel($someChannel, $expectedNumberOfMediasForSomeChannel, false);
        $this->channel->refresh();

        $medias = Media::ungrabbedMediasForChannel($someChannel);
        $this->assertInstanceOf(Collection::class, $medias);
        $this->assertCount($expectedNumberOfMediasForSomeChannel, $medias);

        // adding another channel with some ungrabbed medias
        $expectedNumberOfMediasForAnotherChannel = 3;

        $anotherChannel = factory(Channel::class)->create();
        $this->addMediasToChannel($anotherChannel, $expectedNumberOfMediasForAnotherChannel, false);
        $anotherChannel->refresh();

        $medias = Media::ungrabbedMediasForChannel($anotherChannel);
        $this->assertCount($expectedNumberOfMediasForAnotherChannel, $medias);

        // adding a grabbed media for someChannel should not change the result
        $this->addMediasToChannel($someChannel, 1, true);
        $medias = Media::ungrabbedMediasForChannel($someChannel);
        $this->assertCount($expectedNumberOfMediasForSomeChannel, $medias);
    }
}
