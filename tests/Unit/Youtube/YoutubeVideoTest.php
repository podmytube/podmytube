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
    public const PROCESSED_VIDEO = 'EePwbhMqEh0';

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
            YoutubeVideo::forMedia(self::PROCESSED_VIDEO)->isAvailable()
        );
    }

    public function testVideoTagsShouldWork()
    {
        $expectedTags = ['dev', 'podmytube'];
        $this->assertEqualsCanonicalizing($expectedTags, YoutubeVideo::forMedia(self::PROCESSED_VIDEO)->tags());
    }

    public function testingIsTaggedShouldWork()
    {
        $this->assertFalse(YoutubeVideo::forMedia('ZD_5_dKzsoc')->isTagged());
        $this->assertTrue(YoutubeVideo::forMedia(self::PROCESSED_VIDEO)->isTagged());
    }

    /**
     * for this one it depends on youtube. I cannot have a test video
     * that is "upcoming". I should have 1000 subscribers on my youtube
     * personnal channel and an upcoming live upcoming forever
     */
    /*
    public function testUpcomingVideo()
    {
        $this->assertFalse((new YoutubeVideo('ZkCJ5KZyyOA'))->isAvailable());
    }
    */
}
