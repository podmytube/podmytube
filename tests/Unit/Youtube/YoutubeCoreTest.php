<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeCore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

/**
 * @internal
 *
 * @coversNothing
 */
class YoutubeCoreTest extends YoutubeTestCase
{
    use RefreshDatabase;

    protected YoutubeCore $abstractCore;

    public function setUp(): void
    {
        parent::setUp();
        // Create a new instance from the Abstract Class
        $this->abstractCore = new class() extends YoutubeCore {};
    }

    /** @test */
    public function abstract_instance_is_working_fine(): void
    {
        $this->assertNotNull($this->abstractCore);
        $this->assertInstanceOf(YoutubeCore::class, $this->abstractCore);
    }

    /** @test */
    public function invalid_endpoint_should_fail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->abstractCore->defineEndpoint('LoremIpsum');
    }

    /** @test */
    public function add_part_without_endpoint_fail(): void
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        $this->abstractCore->addParts(['id', 'snippet']);
    }

    /** @test */
    public function totally_empty_response_should_throw_exception(): void
    {
        $this->fakeTotallyEmptyResponse();
        $this->expectException(YoutubeNoResultsException::class);
        $this->abstractCore
            ->defineEndpoint(YoutubeCore::CHANNELS_ENDPOINT)
            ->addParts(['id'])
            ->addParams(['id' => 'ForSureThisChannelIdIsInvalid'])
            ->run()
        ;
    }

    /** @test */
    public function invalid_channel_should_throw_exception(): void
    {
        $this->fakeEmptyChannelResponse();

        $this->expectException(YoutubeNoResultsException::class);
        $this->abstractCore
            ->defineEndpoint(YoutubeCore::CHANNELS_ENDPOINT)
            ->addParts(['id'])
            ->addParams(['id' => 'ForSureThisChannelIdIsInvalid'])
            ->run()
        ;
    }

    /** @test */
    public function getting_proper_id_for_channel_list_should_be_ok(): void
    {
        $expectedTitle = 'PewDiePie';
        $expectedDescription = 'I make videos.';
        $this->fakeChannelResponse(
            expectedChannelId: self::PEWDIEPIE_CHANNEL_ID,
            expectedTitle: $expectedTitle,
        );

        $youtubeCore = $this->abstractCore
            ->defineEndpoint(YoutubeCore::CHANNELS_ENDPOINT)
            ->addParts(['id'])
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
        ;

        $this->assertCount(1, $youtubeCore->queriesUsed());
        $this->assertCount(1, $youtubeCore->items());
        $this->assertEquals(self::PEWDIEPIE_CHANNEL_ID, $youtubeCore->items()[0]['id']);
        $this->assertEquals($expectedTitle, $youtubeCore->items()[0]['snippet']['title']);
        $this->assertEquals($expectedDescription, $youtubeCore->items()[0]['snippet']['description']);
    }

    /** @test */
    public function getting_proper_id_for_playlist_list_should_be_ok(): void
    {
        $this->fakePlaylistResponse(expectedChannelId: self::NOWTECH_CHANNEL_ID);

        $youtubeCore = $this->abstractCore
            ->defineEndpoint(YoutubeCore::PLAYLISTS_ENDPOINT)
            ->addParts(['id', 'snippet'])
            ->addParams([
                'channelId' => self::NOWTECH_CHANNEL_ID,
                'maxResults' => 50,
            ])
            ->run()
        ;

        $this->assertEquals(self::NOWTECH_CHANNEL_ID, $youtubeCore->channelId());
        $this->assertCount(1, $youtubeCore->queriesUsed());
        $this->assertCount(2, $youtubeCore->items());
    }

    /** @test */
    public function getting_next_page_token_should_be_ok(): void
    {
        $this->fakePlaylistResponse(expectedChannelId: self::NOWTECH_CHANNEL_ID, withNextPageToken: true);

        $youtubeCore = $this->abstractCore
            ->defineEndpoint(YoutubeCore::PLAYLISTS_ENDPOINT)
            ->addParts(['id', 'snippet'])
            ->addParams([
                'channelId' => self::NOWTECH_CHANNEL_ID,
                'maxResults' => 50,
            ])
            ->run()
        ;

        // even if I dont change the nextPageToken from the fixture file
        // nextPageToken May only be used once.
        // its a mecanism to avoid falling in infinite loop
        $this->assertCount(2, $youtubeCore->queriesUsed());
    }

    /** @test */
    public function getting_proper_id_for_playlist_items_list_should_be_ok(): void
    {
        $this->fakePlaylistItemsResponse(
            expectedPlaylistId: self::PERSONAL_UPLOADS_PLAYLIST_ID,
            expectedChannelId: self::PERSONAL_CHANNEL_ID,
        );
        $items = $this->abstractCore
            ->defineEndpoint(YoutubeCore::PLAYLIST_ITEMS_ENDPOINT)
            ->addParts(['id', 'snippet'])
            ->addParams([
                'playlistId' => self::PERSONAL_UPLOADS_PLAYLIST_ID,
            ])
            ->run()
            ->items()
        ;

        $this->assertCount(2, $items);
        array_map(function ($item): void {
            $this->assertEquals(
                self::PERSONAL_CHANNEL_ID,
                $item['snippet']['channelId']
            );
        }, $items);
    }
}
