<?php

namespace Tests\Unit;

use App\ApiKey;
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

    public function testEndpointUrlOk()
    {
        $this->assertEquals(
            'https://www.googleapis.com/youtube/v3/channels?key=' .
                $this->apikey .
                '&part=',
            YoutubeCore::init($this->apikey)
                ->endpoint('channels.list')
                ->url()
        );
    }

    public function testAddSinglePartOk()
    {
        $this->assertEquals(
            $this->expectedBaseUrl,
            YoutubeCore::init($this->apikey)
                ->endpoint('channels.list')
                ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
                ->addPart('id')
                ->url()
        );
    }

    public function testAddManyPartsAsStringOk()
    {
        $this->assertEquals(
            $this->expectedBaseUrl . '%2Csnippet%2CcontentDetails',
            YoutubeCore::init($this->apikey)
                ->endpoint('channels.list')
                ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
                ->addParts(['id', 'snippet', 'contentDetails'])
                ->url()
        );
    }

    public function testChannelsListWithSomePartsAsStringWillUseValidUrl()
    {
        $this->assertEquals(
            $this->expectedBaseUrl . '%2Csnippet%2CcontentDetails',
            YoutubeCore::init($this->apikey)
                ->endpoint('channels.list')
                ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
                ->addParts('id,snippet,contentDetails')
                ->url()
        );
    }

    public function testInvalidPartsShouldNotBeSendToYoutube()
    {
        $this->assertEquals(
            $this->expectedBaseUrl . '%2Csnippet%2CcontentDetails',
            YoutubeCore::init($this->apikey)
                ->endpoint('channels.list')
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
}
