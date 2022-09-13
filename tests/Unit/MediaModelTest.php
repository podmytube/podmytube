<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\SendFileBySFTP;
use App\Models\Channel;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MediaModelTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected Channel $channel;

    protected Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create(['explicit' => false]);
        $this->media = Media::factory()->create(['channel_id' => $this->channel->channel_id]);
    }

    public function tearDown(): void
    {
        if (file_exists($this->media->uploadedFilePath())) {
            unlink($this->media->uploadedFilePath());
        }
        parent::tearDown();
    }

    public function test_published_between_should_be_fine(): void
    {
        $expectedNbMedias = 3;
        Media::factory()->count($expectedNbMedias)->create([
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
        Media::factory()->count($expectedNbMedias)->create([
            'channel_id' => $this->channel->channel_id,
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
        // $this->media->refresh();
        $this->assertTrue($this->media->isGrabbed());
    }

    public function test_grabbed_at_should_be_fine(): void
    {
        $expectedResult = 3;
        Media::factory()
            ->grabbedAt(now())
            ->count($expectedResult)->create([
                'channel_id' => $this->channel->channel_id,
            ]);

        $this->assertEquals($expectedResult, Media::grabbedAt()->count());
    }

    /** @test */
    public function by_media_id_is_fine(): void
    {
        $this->assertNull(Media::byMediaId('ThisIsNotAMediaId'));
        $this->assertEquals($this->media->title, Media::byMediaId($this->media->media_id)->title);

        /** same with deleted media */
        $deletedMedia = Media::factory()->create(['deleted_at' => now()]);
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
        $channel = Channel::factory()->create(['explicit' => true]);
        $media = Media::factory()->create(['channel_id' => $channel->channel_id]);
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
        $media = Media::factory()->create([
            'media_id' => $this->faker->regexify('[a-zA-Z0-9-_]{8}'),
            'channel_id' => $this->channel->channel_id,
            'title' => null,
            'description' => null,
            'published_at' => $this->faker->dateTimeBetween(Carbon::now()->startOfMonth(), Carbon::now()),
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
        $media = Media::factory()->create(['uploaded_by_user' => false]);
        $this->assertFalse($media->isUploadedByUser());

        $media = Media::factory()->create(['uploaded_by_user' => true]);
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

        $anotherChannel = Channel::factory()->create();
        $this->addMediasToChannel($anotherChannel, $expectedNumberOfMediasForAnotherChannel, false);
        $anotherChannel->refresh();

        $medias = Media::ungrabbedMediasForChannel($anotherChannel);
        $this->assertCount($expectedNumberOfMediasForAnotherChannel, $medias);

        // adding a grabbed media for someChannel should not change the result
        $this->addMediasToChannel($someChannel, 1, true);
        $medias = Media::ungrabbedMediasForChannel($someChannel);
        $this->assertCount($expectedNumberOfMediasForSomeChannel, $medias);
    }

    /** @test */
    public function weight_is_fine(): void
    {
        $expectedWeight = 0;
        $media = Media::factory()->create();
        $result = $media->weight();
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($expectedWeight, $result);

        $expectedWeight = 455;
        $media = Media::factory()->grabbedAt(now())->create(['length' => $expectedWeight]);
        $result = $media->weight();
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($expectedWeight, $result);
    }

    /** @test */
    public function delete_should_set_active_to_0_and_fill_deleted(): void
    {
        $this->assertNull($this->media->deleted_at);

        $this->media->delete();

        $this->assertNotNull($this->media->deleted_at, 'Deleted_at should be set.');
        $this->assertEquals(now()->toDateString(), $this->media->deleted_at->toDateString());
    }

    /** @test */
    public function real_status_should_be_fine(): void
    {
        array_map(
            function (int $status): void {
                $media = Media::factory()->create(['status' => $status]);
                $this->assertEquals($status, $media->realStatus);

                // if deleted ... we don't care about status
                $media->delete();
                $this->assertEquals(Media::STATUS_DELETED, $media->realStatus);
            },
            [
                Media::STATUS_NOT_DOWNLOADED,
                Media::STATUS_DOWNLOADED,
                Media::STATUS_UPLOADED_BY_USER,
                Media::STATUS_TAG_FILTERED,
                Media::STATUS_AGE_FILTERED,
                Media::STATUS_NOT_PROCESSED_ON_YOUTUBE,
                Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE,
                Media::STATUS_DELETED,
                Media::STATUS_EXHAUSTED_QUOTA,
            ]
        );
    }

    /** @test */
    public function is_deleted_should_be_fine(): void
    {
        $expectations = [
            Media::STATUS_NOT_DOWNLOADED => false,
            Media::STATUS_DOWNLOADED => false,
            Media::STATUS_UPLOADED_BY_USER => false,
            Media::STATUS_TAG_FILTERED => false,
            Media::STATUS_AGE_FILTERED => false,
            Media::STATUS_NOT_PROCESSED_ON_YOUTUBE => false,
            Media::STATUS_NOT_AVAILABLE_ON_YOUTUBE => false,
            Media::STATUS_DELETED => true,
            Media::STATUS_EXHAUSTED_QUOTA => false,
        ];

        array_map(
            function (int $status, bool $expected): void {
                $media = Media::factory()->create(['status' => $status]);

                $this->assertEquals($expected, $media->isDisabled());
            },
            array_keys($expectations),
            $expectations
        );
    }
}
