<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\Unit\Youtube\YoutubeTestCase;

trait IsFakingYoutube
{
    protected array $endpointsShorted = [
        YoutubeTestCase::CHANNELS_INDEX => YoutubeTestCase::CHANNELS_ENDPOINT,
        YoutubeTestCase::PLAYLISTS_INDEX => YoutubeTestCase::PLAYLISTS_ENDPOINT,
        YoutubeTestCase::PLAYLIST_ITEMS_INDEX => YoutubeTestCase::PLAYLIST_ITEMS_ENDPOINT,
        YoutubeTestCase::VIDEOS_INDEX => YoutubeTestCase::VIDEOS_ENDPOINT,
    ];

    protected function fakeEmptyChannelResponse(): void
    {
        Http::fake([
            YoutubeTestCase::CHANNELS_ENDPOINT . '*' => Http::response(
                file_get_contents(fixtures_path('Youtube/empty-channels-response.json')),
                200
            ),
        ]);
    }

    protected function fakeEmptyPlaylistItemsResponse(): void
    {
        Http::fake([
            YoutubeTestCase::PLAYLIST_ITEMS_INDEX . '*' => Http::response(
                file_get_contents(fixtures_path('Youtube/empty-channels-response.json')),
                200
            ),
        ]);
    }

    protected function fakeYoutubeItemNotFound(): void
    {
        Http::fake([
            YoutubeTestCase::PLAYLIST_ITEMS_ENDPOINT . '*' => Http::response(
                file_get_contents(fixtures_path('Youtube/playlist-not-found.json')),
                200
            ),
            YoutubeTestCase::PLAYLISTS_ENDPOINT . '*' => Http::response(
                file_get_contents(fixtures_path('Youtube/channel-not-found.json')),
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
            YoutubeTestCase::CHANNELS_INDEX,
            expectedChannelId: $expectedChannelId,
            expectedTitle: $expectedTitle,
            expectedPlaylistId: $expectedPlaylistId,
        );
    }

    protected function fakePlaylistResponse(string $expectedChannelId, ?string $expectedPlaylistId = null): void
    {
        $this->fakeYoutubeResponse(
            YoutubeTestCase::PLAYLISTS_INDEX,
            expectedChannelId: $expectedChannelId,
            expectedPlaylistId: $expectedPlaylistId
        );
    }

    protected function fakePlaylistItemsResponse(
        string $expectedPlaylistId,
        ?string $expectedChannelId = null,
    ): void {
        $this->fakeYoutubeResponse(
            YoutubeTestCase::PLAYLIST_ITEMS_INDEX,
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
            YoutubeTestCase::VIDEOS_INDEX,
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
            file_get_contents(fixtures_path('Youtube/' . $endpoint . '-response.json'))
        );

        Http::fake([
            $this->endpointsShorted[$endpoint] . '*' => Http::response($expectedJson, 200),
        ]);
    }
}
