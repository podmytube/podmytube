<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideoTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testInvalidMediaShouldThrowException()
    {
        $this->expectException(YoutubeMediaDoesNotExistException::class);
        YoutubeVideo::forMedia('media-that-will-never-exist');
    }

    public function testVideoIsAvailable()
    {
        $this->assertTrue(
            YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isAvailable()
        );
    }

    public function testVideoTagsShouldWork()
    {
        $expectedTags = ['dev', 'podmytube'];
        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->tags());
    }

    public function testingIsTaggedShouldWork()
    {
        $this->assertFalse(YoutubeVideo::forMedia('ZD_5_dKzsoc')->isTagged());
        $this->assertTrue(YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isTagged());
    }

    /**
     * for this one it depends on youtube. I cannot have a test video
     * that is "upcoming". I should have 1000 subscribers on my youtube
     * personnal channel and an upcoming live upcoming forever
     */
    public function testIsAvailableShouldBeGood()
    {
        $this->assertTrue(YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->isAvailable());
    }

    public function testDurationIsWorkingFine()
    {
        $expectedDuration = 285;
        $this->assertEquals($expectedDuration, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->duration());
    }

    public function testTitleIsWorkingFine()
    {
        $expectedTitle = '2015 10 20 Natacha Christian versus Nolwen Fred 01';
        $this->assertEquals($expectedTitle, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->title());
    }

    public function testDescriptionIsWorkingFine()
    {
        $expectedDescription = "20 octobre 2015 - Stade des 3 moulins. 2 duos mixtes s'affrontent dans un match de beach volley. Sans doute pas le plus violent de la saison :)";
        $this->assertEquals($expectedDescription, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->description());
    }

    public function testingVideoIdIsOk()
    {
        $this->assertEquals(self::BEACH_VOLLEY_VIDEO_1, YoutubeVideo::forMedia(self::BEACH_VOLLEY_VIDEO_1)->videoId());
    }

    public function testSpecialHasan()
    {
        $expectedTags = ['podcast'];
        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia('5xHkilEZlFA')->tags());
    }
}
