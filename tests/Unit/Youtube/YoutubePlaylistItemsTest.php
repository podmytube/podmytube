<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistItemsTest extends TestCase
{
    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testHavingTheRightNumberOfItemsInPlaylist()
    {
        $this->assertCount(
            2,
            ($videos = new YoutubePlaylistItems())
                ->forPlaylist(self::MY_PERSONAL_UPLOADS_PLAYLIST_ID)
                ->videos()
        );
        /**
         * base : 1
         * id : 0
         * snippet : 2
         * contentDetails : 2
         */

        $this->assertEqualsCanonicalizing(
            [$videos->apikey() => 5],
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }

    public function testingStrangeCase()
    {
        $videos = (new YoutubePlaylistItems())
                ->forPlaylist('UUpUYA50vUrHvdTyKe6zCZGQ')
                ->videos();
        dd($videos);
    }
}
