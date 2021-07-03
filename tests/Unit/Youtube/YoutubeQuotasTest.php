<?php

declare(strict_types=1);

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeQuotas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class YoutubeQuotasTest extends TestCase
{
    use RefreshDatabase;

    public function test_simple_url_quota_cost_should_be_ok(): void
    {
        $expectedResult = ['AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk' => 7];
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails%2Cstatus',
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }

    public function test_many_urls_should_be_ok(): void
    {
        $expectedResult = ['AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk' => 16];
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id', // 1
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet', // 3
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails', // 5
            'https://www.googleapis.com/youtube/v3/channels?key=AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk&id=UC-lHJZR3Gqxm24_Vd_AJ5Yw&part=id%2Csnippet%2CcontentDetails%2Cstatus', // 7
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }

    public function test_using_many_key_should_be_ok(): void
    {
        $expectedResult = ['key1' => 8, 'key2' => 3];
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=key1&part=id%2Csnippet', // 3
            'https://www.googleapis.com/youtube/v3/channels?key=key2&part=id%2Csnippet%2CinvalidPartParams', // 3
            'https://www.googleapis.com/youtube/v3/channels?key=key1&part=id%2Csnippet%2CcontentDetails', // 5
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }

    public function test_invalid_end_point_should_throw_an_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        YoutubeQuotas::forUrls([
            'https://www.googleapis.com/invalidEndpoint',
        ])->quotaConsumed();
    }

    public function test_invalid_endpoint_should_still_return_some_results(): void
    {
        $expectedResult = ['key1' => 3];
        $queries = [
            'https://www.googleapis.com/youtube/v3/channels?key=key1&part=id%2Csnippet%2CinvalidPartParams', // 3
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            YoutubeQuotas::forUrls($queries)->quotaConsumed()
        );
    }
}
