<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ApiKey;
use App\Models\Quota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class QuotaModelTest extends TestCase
{
    use RefreshDatabase;

    protected ApiKey $apikey;

    public function setUp(): void
    {
        parent::setUp();
        // this key will be used in every test
        $this->apikey = ApiKey::factory()->create();
    }

    /** @test */
    public function by_script_should_be_good(): void
    {
        /** preparation */
        $scriptName = 'lorem.php';
        $expectedNumber = 5;
        Quota::factory()->count($expectedNumber)->create([
            'apikey_id' => $this->apikey->id,
            'script' => $scriptName,
        ]);

        // checking results
        $this->assertCount(5, Quota::byScript($scriptName));
        $this->assertCount(0, Quota::byScript('NeverCallAScript.cpp'));
    }

    /** @test */
    public function saving_single_consumption_should_be_good(): void
    {
        /** preparation */
        $expectedQuotaConsumed = 20;
        $scriptName = 'lorem.php';
        $quotaConsumed = [
            $this->apikey->apikey => $expectedQuotaConsumed,
        ];

        // using function to be tested
        Quota::saveScriptConsumption($scriptName, $quotaConsumed);

        /** checking results */
        $results = Quota::byScript($scriptName);
        $this->assertNotNull($results);
        $this->assertEquals($results->first()->quota_used, $expectedQuotaConsumed);
    }

    /** @test */
    public function saving_multiple_consumption_should_be_good(): void
    {
        /**
         * preparation - this script will use 3 calls to youtube.
         */
        $anotherApikey = ApiKey::factory()->create();
        $lastApikey = ApiKey::factory()->create();
        $scriptName = 'ipsum.php';
        $quotaConsumed = [
            $this->apikey->apikey => 10,
            $anotherApikey->apikey => 20,
            $lastApikey->apikey => 5,
        ];

        // using function to be tested
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
