<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeVideo;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideoTest extends TestCase
{
    public const PROCESSED_VIDEO = 'EePwbhMqEh0';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testVideoIsAvailable()
    {
        $this->assertTrue(
            (new YoutubeVideo(self::PROCESSED_VIDEO))->isAvailable()
        );
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
