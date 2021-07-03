<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Youtube\YoutubeVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeVideoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function test_invalid_media_should_throw_exception(): void
    {
        $this->expectException(YoutubeMediaDoesNotExistException::class);
        YoutubeVideo::forMedia('media-that-will-never-exist');
    }

    public function test_video_is_available(): void
    {
        $this->assertTrue(
            YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isAvailable()
        );
    }

    public function test_video_tags_should_work(): void
    {
        $expectedTags = ['dev', 'podmytube'];
        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->tags());
    }

    public function testing_is_tagged_should_work(): void
    {
        $this->assertFalse(YoutubeVideo::forMedia('ZD_5_dKzsoc')->isTagged());
        $this->assertTrue(YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isTagged());
    }

    /**
     * for this one it depends on youtube. I cannot have a test video
     * that is "upcoming". I should have 1000 subscribers on my youtube
     * personnal channel and an upcoming live upcoming forever.
     */
    public function test_is_available_should_be_good(): void
    {
        $this->assertTrue(YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isAvailable());
    }

    public function test_duration_is_working_fine(): void
    {
        $expectedDuration = 285;
        $this->assertEquals($expectedDuration, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->duration());
    }

    public function test_title_is_working_fine(): void
    {
        $expectedTitle = '2015 10 20 Natacha Christian versus Nolwen Fred 01';
        $this->assertEquals($expectedTitle, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->title());
    }

    public function test_description_is_working_fine(): void
    {
        $expectedDescription = "20 octobre 2015 - Stade des 3 moulins. 2 duos mixtes s'affrontent dans un match de beach volley. Sans doute pas le plus violent de la saison :)";
        $this->assertEquals($expectedDescription, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->description());
    }

    public function testing_video_id_is_ok(): void
    {
        $this->assertEquals(self::BEACH_VOLLEY_VIDEO_1, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->videoId());
    }

    public function test_special_hasan(): void
    {
        $expectedTags = ['podcast'];
        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia('5xHkilEZlFA')->tags());
    }

    /** @test */
    public function tags_is_ok(): void
    {
        $this->assertEqualsCanonicalizing(['dev', 'podmytube'], YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->tags());
        $this->assertEqualsCanonicalizing([], YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_2)->tags());
    }
}
