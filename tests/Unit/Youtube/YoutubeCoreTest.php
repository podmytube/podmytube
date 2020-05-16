<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Exceptions\YoutubeInvalidEndpointException;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeCoreTest extends TestCase
{
    protected $apikey;

    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const PERSONAL_CHANNEL_NB_OF_PLAYLISTS = 2;
    public const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
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
                YoutubeCore::init($this->apikey)
                    ->defineEndpoint($expectedEndPoint)
                    ->endpoint()
            );
        }, $expectedEndPoints);
    }

    public function testEndpointInvalid()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        YoutubeCore::init($this->apikey)->defineEndpoint('LoremIpsum');
    }

    public function testAddPartWithoutEndpointFail()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        YoutubeCore::init($this->apikey)->addParts(['id', 'snippet']);
    }

    public function testGettingProperIdShouldBeOk()
    {
        $results = YoutubeCore::init($this->apikey)
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
}
