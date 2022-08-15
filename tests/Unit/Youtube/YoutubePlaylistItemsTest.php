<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Youtube\YoutubePlaylistItems;
use App\Youtube\YoutubeQuotas;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 * @coversNothing
 */
class YoutubePlaylistItemsTest extends YoutubeTestCase
{
    use RefreshDatabase;

    /** @test */
    public function having_the_right_number_of_items_in_playlist(): void
    {
        $this->fakePlaylistItemsResponse(
            expectedPlaylistId: 'UUw6bU9JT_Lihb2pbtqAUGQw',
        );
        $expectedVideosOnMyChannel = 2;
        $expectedQuotaConsumed = 5;
        $videos = new YoutubePlaylistItems();
        $this->assertCount(
            $expectedVideosOnMyChannel,
            $videos->forPlaylist('UUw6bU9JT_Lihb2pbtqAUGQw')->videos(),
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

    /** @test */
    public function getting_playlist_that_does_not_exist_should_throw_exception(): void
    {
        $this->fakeYoutubeItemNotFound();
        $this->expectException(YoutubeGenericErrorException::class);
        (new YoutubePlaylistItems())->forPlaylist('UUEmWzBUF53cVPhHTnUnsNMw');
    }
}
