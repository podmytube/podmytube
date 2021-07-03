<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubePlaylistItemsTest extends TestCase
{
    use RefreshDatabase;

    protected const MY_PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function test_having_the_right_number_of_items_in_playlist(): void
    {
        $expectedVideosOnMyChannel = 2;
        $expectedQuotaConsumed = 5;
        $videos = new YoutubePlaylistItems();
        $this->assertCount(
            $expectedVideosOnMyChannel,
            $videos->forPlaylist(self::MY_PERSONAL_UPLOADS_PLAYLIST_ID)->videos(),
            "I should have only {$expectedVideosOnMyChannel} uploaded videos on my personnal channel."
        );
        /*
         * id : 0
         * base : 1
         * snippet : 2
         * contentDetails : 2
         */
        $this->assertEqualsCanonicalizing(
            [$videos->apikey() => $expectedQuotaConsumed],
            YoutubeQuotas::forUrls($videos->queriesUsed())->quotaConsumed()
        );
    }

    public function test_published_at(): void
    {
        $this->assertInstanceOf(Carbon::class, (new Carbon(''))->setTimezone('UTC'));
        $this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));

        $this->assertInstanceOf(Carbon::class, Carbon::parse());

        $this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));

        //$this->assertInstanceOf(Carbon::class, Carbon::parse('2012-06-24T21:42:04Z'));
    }

    public function test_getting_playlist_that_does_not_exist_should_throw_exception(): void
    {
        $this->expectException(YoutubeNoResultsException::class);
        (new YoutubePlaylistItems())->forPlaylist('UUEmWzBUF53cVPhHTnUnsNMw');
    }
}
