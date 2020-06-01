<?php

namespace Tests\Unit;

use App\ApiKey;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    protected $developmentKeys;
    protected $productionKeys;

    public function setUp(): void
    {
        parent::setUp();

        // when only this test is run no problem.
        // however sometime RefreshDatabases does not clear table.
        // so I'm forcing the truncate to me made
        DB::table('api_keys')->delete();

        $this->developmentKeys = factory(ApiKey::class, 2)
            ->create([
                'environment' => ApiKey::LOCAL_ENV,
            ])
            ->map(function ($apikey) {
                return $apikey->apikey;
            });
        $this->productionKeys = factory(ApiKey::class, 3)
            ->create([
                'environment' => ApiKey::PROD_ENV,
            ])
            ->map(function ($apikey) {
                return $apikey->apikey;
            });
    }

    public function testGetOneLocalIsRunningFine()
    {
        Config::set('app.env', 'local');
        $apikey = Apikey::make()->get();
        $this->assertTrue($this->developmentKeys->contains($apikey));
    }

    public function testGetOneProductionIsRunningFine()
    {
        Config::set('app.env', 'production');
        $apikey = Apikey::make()->get();
        $this->assertTrue($this->productionKeys->contains($apikey));
    }
}
