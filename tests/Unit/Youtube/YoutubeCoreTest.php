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
    protected $expectedBaseUrl;

    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->expectedBaseUrl =
            'https://www.googleapis.com/youtube/v3/channels?' .
            'id=' .
            self::PEWDIEPIE_CHANNEL_ID .
            '&key=' .
            $this->apikey .
            '&part=id';
    }

    public function testEndpointOk()
    {
        $this->assertEquals(
            'channels.list',
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->endpoint()
        );
    }

    public function testPartParamsOk()
    {
        $expectedPartParams = ['id', 'snippet', 'contentDetails'];
        $this->assertEqualsCanonicalizing(
            $expectedPartParams,
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParts(['id', 'snippet', 'contentDetails'])
                ->partParams()
        );
    }

    public function testEndpointInvalid()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        YoutubeCore::init($this->apikey)->defineEndpoint('LoremIpsum');
    }

    public function testPartParamsFilteredOk()
    {
        $expectedPartParams = ['id', 'snippet', 'contentDetails'];
        $this->assertEqualsCanonicalizing(
            $expectedPartParams,
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParts(['id', 'lorem ipsum', 'snippet', 'contentDetails'])
                ->partParams()
        );
    }

    public function testAddPartWithoutEndpointFail()
    {
        $this->expectException(YoutubeInvalidEndpointException::class);
        YoutubeCore::init($this->apikey)->addParts(['id', 'snippet']);
    }

    public function testEndpointUrlOk()
    {
        $this->assertEquals(
            'https://www.googleapis.com/youtube/v3/channels?key=' .
                $this->apikey .
                '&part=',
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->url()
        );
    }

    public function testAddManyPartsAsStringOk()
    {
        $this->assertEquals(
            $this->expectedBaseUrl . '%2Csnippet%2CcontentDetails',
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
                ->addParts(['id', 'snippet', 'contentDetails'])
                ->url()
        );
    }

    public function testInvalidPartsShouldNotBeSendToYoutube()
    {
        $this->assertEquals(
            $this->expectedBaseUrl .
                '%2Csnippet%2CcontentDetails%2CcontentOwnerDetails',
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
                ->addParts([
                    'id',
                    'snippet',
                    'invalidPart',
                    'contentDetails',
                    'player',
                    'contentOwnerDetails',
                ])
                ->url()
        );
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
