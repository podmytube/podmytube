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
        $this->fakeChannelResponse(expectedChannelId: self::PEWDIEPIE_CHANNEL_ID);

        $items = $this->abstractCore
            ->defineEndpoint(YoutubeCore::CHANNELS_ENDPOINT)
            ->addParts(['id'])
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->items()
        ;

        $this->assertEquals(self::PEWDIEPIE_CHANNEL_ID, $items[0]['id']);
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

    /** @test */
    public function getting_all_playlist_items_by_page_is_ok(): void
    {
        $this->markTestSkipped('TO BE DONE');

        /*
         * nowtech has more then 15 playlists.
         * this function is testing pagination
         */
        $this->assertGreaterThanOrEqual(
            20,
            count(
                $this->abstractCore
                    ->defineEndpoint(YoutubeCore::PLAYLIST_ITEMS_ENDPOINT)
                    ->addParams([
                        'playlistId' => self::NOWTECH_PLAYLIST_ID,
                        'maxResults' => 15,
                    ])
                    ->addParts(['id', 'snippet', 'contentDetails'])
                    ->run()
                    ->items()
            )
        );
    }

    /** @test */
    public function combining_limits_and_max_results(): void
    {
        $this->markTestSkipped('TO BE DONE');

        $uploadsId = self::PEWDIEPIE_CHANNEL_ID;
        $uploadsId[1] = 'U';
        /*
         * we are asking for 35 items on each request
         * we are setting a limit to 60
         * at the end we should have :
         * - 2 queries and
         * - 70 items (2x35)
         * it's more than 60 so it should stop after 2 queries
         */
        $this->abstractCore
            ->defineEndpoint(YoutubeCore::PLAYLIST_ITEMS_ENDPOINT)
            ->setLimit(60)
            ->addParams([
                'playlistId' => $uploadsId,
                'maxResults' => 35,
            ])
            ->addParts(['id', 'snippet'])
            ->run()
        ;

        $this->assertCount(2, $this->abstractCore->queriesUsed());
        $this->assertCount(70, $this->abstractCore->items());
    }
}
