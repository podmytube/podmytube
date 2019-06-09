<?php

namespace Tests\Feature;

use App\Services\YoutubeChannelCheckingService;
use Tests\TestCase;

//use Illuminate\Foundation\Testing\RefreshDatabase;

class YoutubeChannelCheckingServiceTest extends TestCase
{
    /**
     * PewDiePie -- biggest youtuber (subscribers number)
     */
    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function testWrongChannelIdShouldThrowException()
    {
        $this->expectException(\Exception::class);
        YoutubeChannelCheckingService::getChannelName("JeDouteQueCeChannelExiste");
    }

    public function testPewDiePieChannelShoudlBeValidForLong()
    {
        $channel_id = self::PEWDIEPIE_CHANNEL_ID;
        $channelName = YoutubeChannelCheckingService::getChannelName($channel_id);
        $this->assertEquals(
            'PewDiePie',
            $channelName,
            "Either PewDiePie channel {https://www.youtube.com/channel/UC-lHJZR3Gqxm24_Vd_AJ5Yw} has been stopped, either there is something wrong.");
    }
}
