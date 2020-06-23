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

    public function testUpcomingVideo()
    {
        $this->assertFalse((new YoutubeVideo('ZkCJ5KZyyOA'))->isAvailable());
    }
}
