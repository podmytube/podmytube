<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeGenericErrorException;
use App\Models\Channel;
use App\Youtube\YoutubePlaylists;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 *
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
        $channel = Channel::factory()->create();
        $this->fakePlaylistResponse(expectedChannelId: $channel->youtube_id);

        $playlists = (new YoutubePlaylists())->forChannel($channel->youtube_id)->playlists();

        // channel should have playlists (from tests/Fixtures/Youtube/playlists-response.json)
        $expectedPlaylists = [
            'FLw6bU9JT_Lihb2pbtqAUGQw1' => [
                'id' => 'FLw6bU9JT_Lihb2pbtqAUGQw1',
                'title' => 'Lorem ipsum dolore sit amet',
                'description' => '',
                'nbVideos' => 0,
            ],
            'FLw6bU9JT_Lihb2pbtqAUGQw2' => [
                'id' => 'FLw6bU9JT_Lihb2pbtqAUGQw2',
                'title' => 'Consectetur adipiscing elit',
                'description' => '',
                'nbVideos' => 7,
            ],
        ];

        $this->assertCount(count($expectedPlaylists), $playlists);
        $this->assertEqualsCanonicalizing(
            $expectedPlaylists,
            $playlists
        );
    }
}
