<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\ApiKey;
use App\Exceptions\YoutubeNoApiKeyAvailableException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    public const APIKEY_NAME = 'flower-power';

    public function setUp(): void
    {
        parent::setUp();
        if (ApiKey::count()) {
            /*
             * !!! DON'T REMOVE THAT !!!
             * sometime RefreshDatabase don't do what it should and some
             * apikeys are still registered.
             */
            DB::table('api_keys')->delete();
        }
    }

    /** @test */
    public function by_apikey_should_be_good(): void
    {
        $this->assertNull(ApiKey::byApikey(self::APIKEY_NAME));

        factory(ApiKey::class)->create(['apikey' => self::APIKEY_NAME]);
        $apikey = ApiKey::byApikey(self::APIKEY_NAME);

        $this->assertNotNull($apikey);
        $this->assertInstanceOf(ApiKey::class, $apikey);
        $this->assertSame($apikey->apikey, $apikey->apikey);
    }

    /** @test */
    public function all_keys_are_depleted_get_one_should_throw_exception(): void
    {
        $this->createDepletedApiKeys(2);
        $this->expectException(YoutubeNoApiKeyAvailableException::class);
        ApiKey::getOne();
    }

    /** @test */
    public function no_quotas_recorded_yet_should_get_one(): void
    {
        factory(ApiKey::class, 2)->create();
        $this->assertNotNull(ApiKey::getOne());
    }

    /** @test */
    public function only_one_key_with_few_quota_used_should_be_good(): void
    {
        $this->createApiKeysWithQuotaUsed(3, 1);
        $this->assertNotNull(ApiKey::getOne());
    }

    /** @test */
    public function get_one_should_return_one_too(): void
    {
        $this->createDepletedApiKeys(2);
        $apikeyThatWillBeSelected = $this->createApiKeysWithQuotaUsed(500)->first();
        $result = ApiKey::getOne();
        $this->assertNotNull($result);
        $this->assertEquals($apikeyThatWillBeSelected->apikey, $result);
    }

    /** @test */
    public function with_used_keys_and_one_available_get_one_should_return_the_available(): void
    {
        // one key with some quota used
        $apikeyThatWontBeSelected = $this->createApiKeysWithQuotaUsed(3900)->first();
        $apikeyThatWillBeSelected = $this->createApiKeysWithQuotaUsed(600)->first();
        $this->createDepletedApiKeys(3);

        $result = ApiKey::getOne();
        $this->assertNotNull($result);
        $this->assertEquals($apikeyThatWillBeSelected->apikey, $result);
    }
}
