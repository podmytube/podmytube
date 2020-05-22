<?php

namespace Tests\Unit\Youtube;

use App\Exceptions\YoutubeInvalidEndpointException;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeCoreTest extends TestCase
{
    /** @var App\Youtube\YoutubeCore $youtubeCore*/
    protected $youtubeCore;

    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const PERSONAL_CHANNEL_NB_OF_PLAYLISTS = 2;
    public const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';
    public const NOWTECH_CHANNEL_ID = 'UCVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_PLAYLIST_ID = 'PL5SLXKZQtnH8CdXkD8NIdIV-w13VMq1f5';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->youtubeCore = YoutubeCore::init();
    }

    public function testEndpointInvalid()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        $this->youtubeCore->defineEndpoint('LoremIpsum');
    }

    public function testAddPartWithoutEndpointFail()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        $this->youtubeCore->addParts(['id', 'snippet']);
    }

    public function testGettingProperIdForChannelListShouldBeOk()
    {
        $items = $this->youtubeCore
            ->defineEndpoint('channels.list')
            ->addParts(['id'])
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->items();

        $this->assertEquals('UC-lHJZR3Gqxm24_Vd_AJ5Yw', $items[0]['id']);
    }

    public function testGettingProperIdForPlaylistListShouldBeOk()
    {
        $items = $this->youtubeCore
            ->defineEndpoint('playlists.list')
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

    public function testGettingOnlyFirstPewDiePiePlaylistsShouldBeQuick()
    {
        $items = $this->youtubeCore
            ->defineEndpoint('playlists.list')
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
        $this->assertGreaterThanOrEqual(
            20,
            count(
                $this->youtubeCore
                    ->defineEndpoint('playlistItems.list')
                    ->clearParams()
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
}
