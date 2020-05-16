<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeChannelListTest extends TestCase
{
    /** @var string $apikey */
    protected $apikey;
    /** @var string $expectedBaseUrl */
    protected $expectedBaseUrl;
    /** @var string $endpointUrl */
    protected $endpointUrl = 'https://www.googleapis.com/youtube/v3/channels';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->expectedBaseUrl =
            $this->endpointUrl .
            '?' .
            'id=' .
            YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID .
            '&key=' .
            $this->apikey .
            '&part=id';
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

    public function testPartParamsCleanedOk()
    {
        $expectedPartParams = ['id', 'snippet', 'contentDetails'];
        $this->assertEqualsCanonicalizing(
            $expectedPartParams,
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('channels.list')
                ->addParts([
                    'id',
                    'lorem ipsum',
                    'snippet',
                    'snippet',
                    'contentDetails',
                ])
                ->partParams()
        );
    }

    public function testEndpointForChannelsListOk()
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

    public function testGettingUploadsPlaylistIsFine()
    {
        $results = YoutubeCore::init($this->apikey)
            ->defineEndpoint('channels.list')
            ->addParts(['id', 'snippet', 'contentDetails'])
            ->addParams(['id' => YoutubeCoreTest::PERSONAL_CHANNEL_ID])
            ->run()
            ->results();
        $this->assertEquals(
            'UUw6bU9JT_Lihb2pbtqAUGQw',
            $results['items'][0]['contentDetails']['relatedPlaylists'][
                'uploads'
            ]
        );
    }
}
