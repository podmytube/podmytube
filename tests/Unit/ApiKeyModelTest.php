<?php

namespace Tests\Unit;

use App\ApiKey;
use App\Exceptions\YoutubeNoApiKeyAvailableException;
use App\Quota;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\ApiKey $apikey */
    protected $apikey;

    public function setUp():void
    {
        parent::setUp();
        if (ApiKey::count()) {
            /**
             * !!! DON'T REMOVE THAT !!!
             * sometime RefreshDatabase don't do what it should and some
             * apikeys are still registered.
             */
            DB::table('api_keys')->delete();
        }
        $this->apikey = factory(ApiKey::class)->create(['apikey' => 'flower-power']);
    }

    public function testingByApikeyShouldBeGood()
    {
        $expectedApikey = 'flower-power';
        $this->assertNotNull(ApiKey::byApikey($expectedApikey));
        $this->assertInstanceOf(ApiKey::class, ApiKey::byApikey($expectedApikey));
        $this->assertEquals($this->apikey->id, ApiKey::byApikey($expectedApikey)->id);
    }

    public function testingGetOneShouldThrowException()
    {
        factory(Quota::class)->create([
            'apikey_id' => $this->apikey->id,
            'quota_used' => Quota::LIMIT_PER_DAY + 1,
        ]);
        $this->expectException(YoutubeNoApiKeyAvailableException::class);
        ApiKey::getOne();
    }

    public function testingGetOneWithNoQuotasShouldReturnOne()
    {
        $this->assertEquals($this->apikey->apikey, ApiKey::getOne());
    }

    public function testingGetOneWith0QuotaShouldReturnOne()
    {
        factory(Quota::class)->create([
            'apikey_id' => $this->apikey->id,
            'quota_used' => 0,
        ]);
        $this->assertEquals($this->apikey->apikey, ApiKey::getOne());
    }

    public function testingGetOneShouldReturnOneToo()
    {
        factory(Quota::class)->create(['apikey_id' => $this->apikey->id, 'quota_used' => 0, ]);
        $notAvailableApiKeys = factory(ApiKey::class, 3)->create();
        foreach ($notAvailableApiKeys as $notAvailableApiKey) {
            factory(Quota::class)->create(['apikey_id' => $notAvailableApiKey->id, 'quota_used' => Quota::LIMIT_PER_DAY + 1]);
        }
        $this->assertEquals($this->apikey->apikey, ApiKey::getOne());
    }
}
