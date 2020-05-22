<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeVideos;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideosTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            2,
            YoutubeVideos::forChannel(
                YoutubeCoreTest::PERSONAL_CHANNEL_ID
            )->videos()
        );
    }
}
