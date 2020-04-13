<?php

namespace Tests\Feature;

use App\Exceptions\YoutubeApiInvalidChannelIdException;
use App\Services\YoutubeChannelCheckingService;
use Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class YoutubeChannelCheckingServiceTest extends TestCase
{
    /**
     * PewDiePie -- biggest youtuber (subscribers number)
     */
    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function setUp(): void
    {
        parent::setUp();
        Config::set('APP_ENV', 'testing');
        Artisan::call('db:seed');
    }

    public function tearDown(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function testWrongChannelIdShouldThrowException()
    {
        $this->expectException(YoutubeApiInvalidChannelIdException::class);
        YoutubeChannelCheckingService::init('JeDouteQueCeChannelExisteUnJour');
    }

    public function testPewDiePieChannelShoudlBeValidForLong()
    {
        $channelName = YoutubeChannelCheckingService::init(
            self::PEWDIEPIE_CHANNEL_ID
        )->getChannelName();
        $this->assertEquals(
            'PewDiePie',
            $channelName,
            'Either PewDiePie channel {https://www.youtube.com/channel/UC-lHJZR3Gqxm24_Vd_AJ5Yw} has been stopped, either there is something wrong.'
        );
    }
}