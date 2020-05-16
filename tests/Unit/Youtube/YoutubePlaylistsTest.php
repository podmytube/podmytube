<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeCore;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubePlaylistsTest extends TestCase
{
    /** @var string $apikey */
    protected $apikey;
    /** @var string $expectedBaseUrl */
    protected $expectedBaseUrl;
    /** @var string $endpointUrl */
    protected $endpointUrl = 'https://www.googleapis.com/youtube/v3/playlists';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->apikey = ApiKey::make()->get();
        $this->expectedBaseUrl =
            $this->endpointUrl . '?' . 'key=' . $this->apikey . '&part=id';
    }

    public function testEndpointForPlaylistsListOk()
    {
        $this->assertEquals(
            $this->endpointUrl .
                '?channelId=' .
                YoutubeCoreTest::PERSONAL_CHANNEL_ID .
                '&key=' .
                $this->apikey .
                '&part=id',
            YoutubeCore::init($this->apikey)
                ->defineEndpoint('playlists.list')
                ->addParts(['id'])
                ->addParams([
                    'channelId' => YoutubeCoreTest::PERSONAL_CHANNEL_ID,
                ])
                ->url()
        );
    }

    public function testPersonalChannelShouldHave2Playlists()
    {
        $youtubeCoreObj = YoutubeCore::init($this->apikey)
            ->defineEndpoint('playlists.list')
            ->addParts(['id', 'snippet'])
            ->addParams([
                'channelId' => YoutubeCoreTest::PERSONAL_CHANNEL_ID,
            ])
            ->run();
        $this->assertCount(
            YoutubeCoreTest::PERSONAL_CHANNEL_NB_OF_PLAYLISTS,
            $youtubeCoreObj->items()
        );

        dump($youtubeCoreObj->items());
    }
}
