<?php

namespace Tests\Unit;

use App\ApiKey;
use App\Exceptions\YoutubeNoApiKeyAvailableException;
use App\Quota;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    public function testingByApikeyShouldBeGood()
    {
        $expectedApikey = 'flower-power';
        $apikeyCreated = factory(ApiKey::class)->create(
            ['apikey' => $expectedApikey]
        );

        $this->assertNotNull(ApiKey::byApikey($expectedApikey));
        $this->assertInstanceOf(ApiKey::class, ApiKey::byApikey($expectedApikey));
        $this->assertEquals($apikeyCreated->id, ApiKey::byApikey($expectedApikey)->id);
    }

    public function testingGetOneShouldThrowException()
    {
        $apikey = factory(ApiKey::class)->create();
        factory(Quota::class)->create([
            'apikey_id' => $apikey->id,
            'quota_used' => Quota::LIMIT_PER_DAY + 1,
        ]);
        $this->expectException(YoutubeNoApiKeyAvailableException::class);
        ApiKey::getOne();
    }

    public function testingGetOneWithNoQuotasShouldReturnOne()
    {
        $apikey = factory(ApiKey::class)->create();
        $this->assertEquals($apikey->apikey, ApiKey::getOne());
    }

    public function testingGetOneWith0QuotaShouldReturnOne()
    {
        $apikey = factory(ApiKey::class)->create();
        factory(Quota::class)->create([
            'apikey_id' => $apikey->id,
            'quota_used' => 0,
        ]);
        $this->assertEquals($apikey->apikey, ApiKey::getOne());
    }

    public function testingGetOneShouldReturnOneToo()
    {
        $availableApiKey = factory(ApiKey::class)->create();
        factory(Quota::class)->create(['apikey_id' => $availableApiKey->id, 'quota_used' => 0, ]);

        $notAvailableApiKeys = factory(ApiKey::class, 3)->create();
        foreach ($notAvailableApiKeys as $apikey) {
            factory(Quota::class)->create(['apikey_id' => $apikey->id, 'quota_used' => Quota::LIMIT_PER_DAY + 1]);
        }

        $this->assertEquals($availableApiKey->apikey, ApiKey::getOne());
    }
}
