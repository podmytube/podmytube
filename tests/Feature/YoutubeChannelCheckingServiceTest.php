<?php

namespace Tests\Feature;

use App\ApiKey;
use App\Exceptions\YoutubeApiInvalidChannelIdException;
use App\Services\YoutubeChannelCheckingService;
use Artisan;
use Illuminate\Support\Facades\Config;
use Madcoda\Youtube\Youtube;
use Tests\TestCase;

class YoutubeChannelCheckingServiceTest extends TestCase
{
    /**
     * PewDiePie -- biggest youtuber (subscribers number)
     */
    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    /** @var \App\ApiKey $apikey */
    protected $apikey;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        Artisan::call('db:seed');
        $this->youtubeObj = new Youtube([
            'key' => ApiKey::make()->getOne(),
        ]);
    }

    public function tearDown(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function testWrongChannelIdShouldThrowException()
    {
        $this->expectException(YoutubeApiInvalidChannelIdException::class);
        YoutubeChannelCheckingService::init(
            $this->youtubeObj,
            'JeDouteQueCeChannelExisteUnJour'
        );
    }

    public function testPewDiePieChannelShoudlBeValidForLong()
    {
        $channelName = YoutubeChannelCheckingService::init(
            $this->youtubeObj,
            self::PEWDIEPIE_CHANNEL_ID
        )->getChannelName();
        $this->assertEquals(
            'PewDiePie',
            $channelName,
            'Either PewDiePie channel {https://www.youtube.com/channel/UC-lHJZR3Gqxm24_Vd_AJ5Yw} has been stopped, either there is something wrong.'
        );
    }
}
