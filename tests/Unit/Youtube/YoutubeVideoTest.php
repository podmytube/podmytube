<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;
use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideoTest extends TestCase
{
    /**
     * this video exists and has 2 tags ['dev','podmytube'];
     */
    public const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';

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
}
