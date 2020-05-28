<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeQuotas;
use App\Youtube\YoutubeVideos;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeVideosTest extends TestCase
{
    /** @var \App\Interfaces\QuotasCalculator quotaCalculator */
    protected $quotaCalculator;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->quotaCalculator = new YoutubeQuotas();
    }

    public function testHavingTheRightNumberOfVideos()
    {
        $this->assertCount(
            2,
            YoutubeVideos::init($this->quotaCalculator)
                ->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID)
                ->videos()
        );
    }
}
