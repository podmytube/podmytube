<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Exceptions\YoutubeInvalidEndpointException;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeCoreTest extends TestCase
{
    /** @var \App\ApiKey $apikey */
    protected $apikey;
    /** @var App\Youtube\YoutubeCore $youtubeCore*/
    protected $youtubeCore;

    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const PERSONAL_CHANNEL_NB_OF_PLAYLISTS = 2;
    public const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->youtubeCore = YoutubeCore::init($this->apikey);
    }

    public function testChannelListEndpointOk()
    {
        $expectedEndPoints = [
            'channels.list',
            'playlistItems.list',
            'playlists.list',
            'search.list',
            'videos.list',
        ];
        array_map(function ($expectedEndPoint) {
            $this->assertEquals(
                $expectedEndPoint,
                $this->youtubeCore
                    ->defineEndpoint($expectedEndPoint)
                    ->endpoint()
            );
        }, $expectedEndPoints);
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
        $results = $this->youtubeCore
            ->defineEndpoint('channels.list')
            ->addParts(['id'])
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->results();

        $this->assertEquals(
            'UC-lHJZR3Gqxm24_Vd_AJ5Yw',
            $results['items'][0]['id']
        );
    }

    public function testGettingProperIdForPlaylistListShouldBeOk()
    {
        $results = $this->youtubeCore
            ->defineEndpoint('playlists.list')
            ->addParts(['id', 'snippet'])
            ->addParams(['channelId' => self::PEWDIEPIE_CHANNEL_ID])
            ->run()
            ->results();

        $this->assertEquals(
            'UC-lHJZR3Gqxm24_Vd_AJ5Yw',
            $results['items'][0]['snippet']['channelId']
        );
    }
}
