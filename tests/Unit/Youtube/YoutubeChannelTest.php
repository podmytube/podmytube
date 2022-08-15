<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeChannelTest extends TestCase
{
    use RefreshDatabase;

    public const CHANNELS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/channels';

    protected YoutubeChannel $youtubeChannel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->youtubeChannel = new YoutubeChannel();
    }

    /** @test */
    public function invalid_channel_id_should_throw_exception(): void
    {
        Http::fake([
            self::CHANNELS_ENDPOINT . '*' => Http::response(
                file_get_contents($this->fixturesPath('Youtube/empty-channels-response.json')),
                200
            ),
        ]);
        $this->expectException(YoutubeNoResultsException::class);
        YoutubeChannel::init()->forChannel('invalid-channel_id');
    }

    /** @test */
    public function obtaining_name_from_valid_channel_id_should_succeed(): void
    {
        $expectedTitle = 'Lorem ipsum dolore sit amet';
        $this->prepareFakeResponse(expectedChannelId: self::BEACH_VOLLEY_VIDEO_1, expectedTitle: $expectedTitle);

        $this->assertEquals($expectedTitle, $this->youtubeChannel->forChannel(YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID)->name());
    }

    /** @test */
    public function another_way_to_obtain_uploads_playlist_id(): void
    {
        $expectedPlaylistId = YoutubeCoreTest::PERSONAL_UPLOADS_PLAYLIST_ID;
        $this->prepareFakeResponse(expectedChannelId: YoutubeCoreTest::PERSONAL_CHANNEL_ID, expectedPlaylistId: $expectedPlaylistId);
        $this->assertEquals(
            $expectedPlaylistId,
            $this->youtubeChannel->forChannel(YoutubeCoreTest::PERSONAL_CHANNEL_ID, ['id', 'snippet', 'contentDetails'])->uploadsPlaylistId()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    public function prepareFakeResponse(
        string $expectedChannelId,
        ?string $expectedTitle = null,
        ?string $expectedPlaylistId = null,
    ): void {
        $expectedJson = str_replace(
            ['EXPECTED_CHANNEL_ID', 'EXPECTED_TITLE', 'EXPECTED_PLAYLIST_ID'],
            [$expectedChannelId, $expectedTitle, $expectedPlaylistId],
            file_get_contents($this->fixturesPath('Youtube/channels-response.json'))
        );

        Http::fake([
            self::CHANNELS_ENDPOINT . '*' => Http::response($expectedJson, 200),
        ]);
    }
}
