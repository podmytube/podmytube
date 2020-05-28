<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistItemsTest extends TestCase
{
    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    /** @var \App\Interfaces\QuotasCalculator quotaCalculator */
    protected $quotaCalculator;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->quotaCalculator = new YoutubeQuotas();
    }

    public function testHavingTheRightNumberOfItemsInPlaylist()
    {
        $this->assertCount(
            2,
            ($videos = YoutubePlaylistItems::init($this->quotaCalculator))
                ->forPlaylist(self::MY_PERSONAL_UPLOADS_PLAYLIST_ID)
                ->videos()
        );
        $this->assertEquals(222, $videos->quotasUsed());
    }
}
