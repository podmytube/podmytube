<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeChannelTest extends YoutubeTestCase
{
    use RefreshDatabase;

    protected YoutubeChannel $youtubeChannel;

    public function setUp(): void
    {
        parent::setUp();
        $this->youtubeChannel = new YoutubeChannel();
    }

    /** @test */
    public function invalid_channel_id_should_throw_exception(): void
    {
        $this->fakeEmptyChannelResponse();
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeChannel::init()->forChannel('invalid-channel_id');
    }

    /** @test */
    public function obtaining_name_from_valid_channel_id_should_succeed(): void
    {
        $expectedTitle = 'Lorem ipsum dolore sit amet';
        $this->fakeChannelResponse(expectedChannelId: self::BEACH_VOLLEY_VIDEO_1, expectedTitle: $expectedTitle);

        $this->assertEquals($expectedTitle, $this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->name());
    }

    /** @test */
    public function another_way_to_obtain_uploads_playlist_id(): void
    {
        $expectedPlaylistId = YoutubeCoreTest::PERSONAL_UPLOADS_PLAYLIST_ID;
        $this->fakeChannelResponse(expectedChannelId: YoutubeCoreTest::PERSONAL_CHANNEL_ID, expectedPlaylistId: $expectedPlaylistId);
        $this->assertEquals(
            $expectedPlaylistId,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }
}
