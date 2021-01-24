<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Exceptions\YoutubeNoResultsException;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use Tests\TestCase;

class YoutubeCoreTest extends TestCase
{
    /** @var App\Youtube\YoutubeCore $youtubeCore*/
    protected $abstractCore;

    public const PERSONAL_UPLOADS_PLAYLIST_ID = 'UUw6bU9JT_Lihb2pbtqAUGQw';
    public const PERSONAL_CHANNEL_NB_OF_PLAYLISTS = 2;
    public const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';
    public const PEWDIEPIE_UPLOADS_PLAYLIST_ID = 'UU-lHJZR3Gqxm24_Vd_AJ5Yw';
    public const NOWTECH_UPLOADS_PLAYLIST_ID = 'UUVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_PLAYLIST_ID = 'PL5SLXKZQtnH8CdXkD8NIdIV-w13VMq1f5';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        // Create a new instance from the Abstract Class
        $this->abstractCore = new class extends YoutubeCore {
            // Just a sample public function that returns this anonymous instance
            public function returnThis()
            {
                return $this;
            }
        };
    }

    public function testingAbstractInstance()
    {
        $this->assertInstanceOf(
            YoutubeCore::class,
            $this->abstractCore->returnThis()
        );
    }

    public function testEndpointInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->abstractCore->defineEndpoint('LoremIpsum');
    }

    public function testAddPartWithoutEndpointFail()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        $this->abstractCore->addParts(['id', 'snippet']);
    }

    public function testInvalidChannelShouldThrowException()
    {
        $this->expectException(YoutubeNoResultsException::class);
        $this->abstractCore
            ->defineEndpoint('/youtube/v3/channels')
            ->addParts(['id'])
            ->addParams(['id' => 'ForSureThisChannelIdIsInvalid'])
            ->run();
    }

    public function testGettingProperIdForChannelListShouldBeOk()
    {
        $items = $this->abstractCore
            ->defineEndpoint('/youtube/v3/channels')
            ->addParts(['id'])
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->items();

        $this->assertEquals('UC-lHJZR3Gqxm24_Vd_AJ5Yw', $items[0]['id']);
    }

    public function testGettingProperIdForPlaylistListShouldBeOk()
    {
        $items = $this->abstractCore
            ->defineEndpoint('/youtube/v3/playlists')
            ->addParts(['id', 'snippet'])
            ->addParams([
                'channelId' => self::NOWTECH_CHANNEL_ID,
                'maxResults' => 50,
            ])
            ->run()
            ->items();

        $this->assertEquals(
            self::NOWTECH_CHANNEL_ID,
            $items[0]['snippet']['channelId']
        );
    }

    public function testGettingProperIdForPlaylistItemsListShouldBeOk()
    {
        $myUploadsPlaylistId = 'UUw6bU9JT_Lihb2pbtqAUGQw';
        $items = $this->abstractCore
            ->defineEndpoint('/youtube/v3/playlistItems')
            ->addParts(['id', 'snippet'])
            ->addParams([
                'playlistId' => $myUploadsPlaylistId,
            ])
            ->run()
            ->items();

        $this->assertCount(2, $items);
        array_map(function ($item) {
            $this->assertEquals(
                self::PERSONAL_CHANNEL_ID,
                $item['snippet']['channelId']
            );
        }, $items);
    }

    public function testGettingOnlyFirstPewDiePiePlaylistsShouldBeQuick()
    {
        $items = $this->abstractCore
            ->defineEndpoint('/youtube/v3/playlists')
            ->setLimit(1)
            ->addParts(['id', 'snippet'])
            ->addParams(['channelId' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->items();

        $this->assertEquals(
            self::PEWDIEPIE_CHANNEL_ID,
            $items[0]['snippet']['channelId']
        );
    }

    public function testGettingAllPlaylistItemsByPageIsOk()
    {
        /**
         * nowtech has more then 15 playlists.
         * this function is testing pagination
         */
        $this->assertGreaterThanOrEqual(
            20,
            count(
                $this->abstractCore
                    ->defineEndpoint('/youtube/v3/playlistItems')
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

    public function testCombiningLimitsAndMaxResults()
    {
        $uploadsId = self::PEWDIEPIE_CHANNEL_ID;
        $uploadsId[1] = 'U';
        /**
         * we are asking for 35 items on each request
         * we are setting a limit to 60
         * at the end we should have :
         * - 2 queries and
         * - 70 items (2x35)
         * it's more than 60 so it should stop after 2 queries
         */
        $this->abstractCore
            ->defineEndpoint('/youtube/v3/playlistItems')
            ->setLimit(60)
            ->addParams([
                'playlistId' => $uploadsId,
                'maxResults' => 35,
            ])
            ->addParts(['id', 'snippet'])
            ->run();

        $this->assertCount(2, $this->abstractCore->queriesUsed());
        $this->assertCount(70, $this->abstractCore->items());
    }
}
