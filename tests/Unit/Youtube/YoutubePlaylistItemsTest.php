<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Carbon\Carbon;
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
        $videos = new YoutubePlaylistItems();
        $this->assertCount(
            2,
            $videos->forPlaylist(self::MY_PERSONAL_UPLOADS_PLAYLIST_ID)->videos()
        );
        /**
         * id : 0
         * base : 1
         * snippet : 2
         * contentDetails : 2
         */

        $this->assertEqualsCanonicalizing(
            [$videos->apikey() => 5],
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }

    public function testPublishedAt()
    {
        $this->assertInstanceOf(Carbon::class, (new Carbon(''))->setTimezone('UTC'));
        $this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));

        $this->assertInstanceOf(Carbon::class, Carbon::parse());

        $this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));

        //$this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));
    }

    public function testGettingPlaylistThatDoesNotExistShouldThrowException()
    {
        $this->expectException(YoutubeNoResultsException::class);
        (new YoutubePlaylistItems())->forPlaylist('UUEmWzBUF53cVPhHTnUnsNMw');
    }
}
