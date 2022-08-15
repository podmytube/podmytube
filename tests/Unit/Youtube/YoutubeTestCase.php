<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeTestCase extends TestCase
{
    public const CHANNELS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/channels';
    public const PLAYLISTS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/playlists';
    public const PLAYLIST_ITEMS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/playlistItems';
    public const VIDEOS_ENDPOINT = 'https://www.googleapis.com/youtube/v3/videos';

    public const CHANNELS_INDEX = 'channels';
    public const PLAYLISTS_INDEX = 'playlists';
    public const PLAYLIST_ITEMS_INDEX = 'playlistItems';
    public const VIDEOS_INDEX = 'videos';

    protected array $endpointsShorted = [
        self::CHANNELS_INDEX => self::CHANNELS_ENDPOINT,
        self::PLAYLISTS_INDEX => self::PLAYLISTS_ENDPOINT,
        self::PLAYLIST_ITEMS_INDEX => self::PLAYLIST_ITEMS_INDEX,
        self::VIDEOS_INDEX => self::VIDEOS_ENDPOINT,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }

    protected function fakeEmptyChannelResponse(): void
    {
        Http::fake([
            self::CHANNELS_ENDPOINT . '*' => Http::response(
                file_get_contents($this->fixturesPath('Youtube/empty-channels-response.json')),
                200
            ),
        ]);
    }

    protected function fakeYoutubeItemNotFound(): void
    {
        Http::fake([
            self::PLAYLIST_ITEMS_ENDPOINT . '*' => Http::response(
                file_get_contents($this->fixturesPath('Youtube/playlist-not-found.json')),
                200
            ),
            self::PLAYLISTS_ENDPOINT . '*' => Http::response(
                file_get_contents($this->fixturesPath('Youtube/channel-not-found.json')),
                200
            ),
        ]);
    }

    protected function fakeChannelResponse(
        string $expectedChannelId,
        ?string $expectedTitle = null,
        ?string $expectedPlaylistId = null,
    ): void {
        $this->fakeYoutubeResponse(
            self::CHANNELS_INDEX,
            expectedChannelId: $expectedChannelId,
            expectedTitle: $expectedTitle,
            expectedPlaylistId: $expectedPlaylistId,
        );
    }

    protected function fakePlaylistResponse(string $expectedChannelId, ?string $expectedPlaylistId = null): void
    {
        $this->fakeYoutubeResponse(
            self::PLAYLISTS_INDEX,
            expectedChannelId: $expectedChannelId,
            expectedPlaylistId: $expectedPlaylistId
        );
    }

    protected function fakePlaylistItemsResponse(
        string $expectedPlaylistId,
        ?string $expectedChannelId = null,
    ): void {
        $this->fakeYoutubeResponse(
            self::PLAYLIST_ITEMS_INDEX,
            expectedPlaylistId: $expectedPlaylistId,
            expectedChannelId: $expectedChannelId
        );
    }

    protected function fakeVideoResponse(
        string $expectedMediaId,
        ?string $expectedTitle = null,
        ?string $expectedDescription = null,
        ?array $expectedTags = [],
        ?string $expectedDuration = null,
    ): void {
        $this->fakeYoutubeResponse(
            self::VIDEOS_INDEX,
            expectedMediaId: $expectedMediaId,
            expectedTitle: $expectedTitle,
            expectedDescription: $expectedDescription,
            expectedTags: $expectedTags,
            expectedDuration: $expectedDuration,
        );
    }

    protected function fakeYoutubeResponse(
        string $endpoint,
        ?string $expectedMediaId = null,
        ?string $expectedPlaylistId = null,
        ?string $expectedChannelId = null,
        ?string $expectedTitle = null,
        ?string $expectedDescription = null,
        ?array $expectedTags = [],
        ?string $expectedDuration = null,
        ?int $totalResults = 1,
        ?int $resultsPerPage = 1
    ): void {
        if (!array_key_exists($endpoint, $this->endpointsShorted)) {
            throw new InvalidArgumentException("{$endpoint} is unknown");
        }

        $tags = count($expectedTags) ? '"' . implode('","', $expectedTags) . '"' : '';
        $expectedJson = str_replace(
            ['EXPECTED_MEDIA_ID', 'EXPECTED_CHANNEL_ID', 'EXPECTED_PLAYLIST_ID', 'EXPECTED_TITLE', 'EXPECTED_DESCRIPTION', 'EXPECTED_TOTAL_RESULTS', 'EXPECTED_RESULTS_PER_PAGE', 'EXPECTED_TAGS', 'EXPECTED_DURATION'],
            [$expectedMediaId, $expectedChannelId, $expectedPlaylistId, $expectedTitle, $expectedDescription, $totalResults, $resultsPerPage, $tags, $expectedDuration],
            file_get_contents($this->fixturesPath('Youtube/' . $endpoint . '-response.json'))
        );

        Http::fake([
            $this->endpointsShorted[$endpoint] . '*' => Http::response($expectedJson, 200),
        ]);
    }
}
