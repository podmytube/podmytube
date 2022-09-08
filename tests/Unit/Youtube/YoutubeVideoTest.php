<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Youtube\YoutubeVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

/**
 * @internal
 *
 * @coversNothing
 */
class YoutubeVideoTest extends YoutubeTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }

    /** @test */
    public function invalid_media_should_throw_exception(): void
    {
        Http::fake([
            self::VIDEOS_ENDPOINT . '*' => Http::response(
                file_get_contents(fixtures_path('Youtube/empty-videos-response.json')),
                200
            ),
        ]);
        $this->expectException(YoutubeMediaDoesNotExistException::class);
        YoutubeVideo::forMedia('media-that-will-never-exist');
    }

    /** @test */
    public function video_is_available(): void
    {
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1);
        $this->assertTrue(
            YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isAvailable()
        );
    }

    /** @test */
    public function video_tags_should_work(): void
    {
        $expectedTags = ['dev', 'podmytube'];
        $this->fakeVideoResponse(
            expectedMediaId: self::BEACH_VOLLEY_VIDEO_1,
            expectedTags: $expectedTags
        );

        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->tags());
    }

    /** @test */
    public function no_video_tags_should_work(): void
    {
        $this->fakeVideoResponse(
            expectedMediaId: self::BEACH_VOLLEY_VIDEO_1,
        );

        $this->assertCount(0, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->tags());
    }

    /** @test */
    public function is_tagged_should_return_false(): void
    {
        $this->fakeVideoResponse(expectedMediaId: 'ZD_5_dKzsoc');
        $this->assertFalse(YoutubeVideo::forMedia('ZD_5_dKzsoc')->isTagged());
    }

    /** @test */
    public function is_tagged_should_return_true(): void
    {
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1, expectedTags: ['dev', 'podmytube']);
        $this->assertTrue(YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isTagged());
    }

    /** @test */
    public function duration_is_working_fine(): void
    {
        $expectedDuration = 285;
        $youtubeDuration = secondsToYoutubeFormat($expectedDuration);
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1, expectedDuration: $youtubeDuration);
        $this->assertEquals(
            $expectedDuration,
            YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->duration()
        );
    }

    /** @test */
    public function title_is_working_fine(): void
    {
        $expectedTitle = 'Lorem ipsum dolore sit amet';
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1, expectedTitle: $expectedTitle);

        $this->assertEquals($expectedTitle, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->title());
    }

    /** @test */
    public function description_is_working_fine(): void
    {
        $expectedDescription = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris eget gravida mauris. Etiam in dictum neque, eu dapibus augue. Phasellus sollicitudin finibus vehicula. Donec dapibus et tellus a ornare.';
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1, expectedDescription: $expectedDescription);
        $this->assertEquals($expectedDescription, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->description());
    }

    /** @test */
    public function video_id_is_ok(): void
    {
        $this->fakeVideoResponse(expectedMediaId: self::BEACH_VOLLEY_VIDEO_1);
        $this->assertEquals(self::BEACH_VOLLEY_VIDEO_1, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->videoId());
    }
}
