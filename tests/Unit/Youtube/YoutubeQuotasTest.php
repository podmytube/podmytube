<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeQuotas;
use Tests\TestCase;

class YoutubeQuotasTest extends TestCase
{
    public function testSimpleUrlQuotaCostShouldBeOk()
    {
        $expectedQuota = 7;
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails%2Cstatus',
        ];
        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }

    public function testManyUrlsShouldBeOk()
    {
        $expectedQuota = 16;
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id', // 1
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet', // 3
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails', // 5
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails%2Cstatus', // 7
        ];
        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }

    public function testInvalidEndPointShouldThrowAnException()
    {
        $this->expectException(\InvalidArgumentException::class);
        YoutubeQuotas::forUrls([
            'https://www.googleapis.com/invalidEndpoint',
        ])->quotaConsumed();
    }

    public function testInvalidEndpointShouldStillReturnSomeResults()
    {
        $expectedQuota = 3;
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?part=id%2Csnippet%2CinvalidPartParams', // 3
        ];
        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }
}
