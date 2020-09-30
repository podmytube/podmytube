<?php

namespace Tests\Unit;

use App\ApiKey;
use App\Quota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\ApiKey $apikey */
    protected $apikey;

    public function setUp(): void
    {
        parent::setUp();
        /** this key will be used in every test */
        $this->apikey = factory(ApiKey::class)->create();
    }

    public function testingByScriptShouldBeGood()
    {
        /** preparation */
        $scriptName = 'lorem.php';
        $expectedNumber = 5;
        factory(Quota::class, $expectedNumber)->create([
            'apikey_id' => $this->apikey->id,
            'script' => $scriptName,
        ]);

        /** checking results */
        $this->assertCount(5, Quota::byScript($scriptName));
        $this->assertCount(0, Quota::byScript('NeverCallAScript.cpp'));
    }

    public function testingSavingSingleConsumptionShouldBeGood()
    {
        /** preparation */
        $expectedQuotaConsumed = 20;
        $scriptName = 'lorem.php';
        $quotaConsumed = [
            $this->apikey->apikey => $expectedQuotaConsumed
        ];

        /** using function to be tested */
        Quota::saveScriptConsumption($scriptName, $quotaConsumed);

        /** checking results */
        $results = Quota::byScript($scriptName);
        $this->assertNotNull($results);
        $this->assertEquals($results->first()->quota_used, $expectedQuotaConsumed);
    }

    public function testingSavingMultipleConsumptionShouldBeGood()
    {
        /** 
         * preparation - this script will use 3 calls to youtube
         */
        $anotherApikey = factory(ApiKey::class)->create();
        $lastApikey = factory(ApiKey::class)->create();
        $scriptName = 'ipsum.php';
        $quotaConsumed = [
            $this->apikey->apikey => 10,
            $anotherApikey->apikey => 20,
            $lastApikey->apikey => 5,
        ];

        /** using function to be tested */
        Quota::saveScriptConsumption($scriptName, $quotaConsumed);

        /** checking results */
        $results = Quota::byScript($scriptName);
        $totalQuotaConsumed = $results->reduce(function ($carry, $QuotaModel) {
            return $carry + $QuotaModel->quota_used;
        });

        $this->assertNotNull($results);
        $this->assertEquals(35, $totalQuotaConsumed);
    }
}
