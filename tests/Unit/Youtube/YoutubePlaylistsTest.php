<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Youtube\YoutubePlaylists;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 * @coversNothing
 */
class YoutubePlaylistsTest extends YoutubeTestCase
{
    use RefreshDatabase;

    /** @test */
    public function invalid_channel_id_should_throw_an_exception(): void
    {
        $this->fakeYoutubeItemNotFound();
        $this->expectException(YoutubeGenericErrorException::class);
        (new YoutubePlaylists())
            ->forChannel('ForSureThisChannelWillNeverEverExist')
        ;
    }

    /** @test */
    public function playlists_is_ok(): void
    {
        $expectedPlaylistId = 'PLQz1j9ftwaG0h5LWWnqgMYPo31PlXZcko';
        $this->fakePlaylistResponse(
            expectedChannelId: self::PERSONAL_CHANNEL_ID,
            expectedPlaylistId: $expectedPlaylistId
        );
        $playlists = (new YoutubePlaylists())->forChannel(self::PERSONAL_CHANNEL_ID)->playlists();
        $this->assertCount(2, $playlists);
        $this->assertArrayHasKey($expectedPlaylistId, $playlists);
        $this->assertEquals('testPmt', $playlists[$expectedPlaylistId]['title']);
        $this->assertEquals(0, $playlists[$expectedPlaylistId]['nbVideos']);
    }
}
