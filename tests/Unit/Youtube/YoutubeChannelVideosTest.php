<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeChannelVideos;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelVideosTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->youtubeCore = YoutubeCore::init($this->apikey);
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            2,
            YoutubeChannelVideos::init($this->youtubeCore)
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->videoIds()
        );
    }
}
