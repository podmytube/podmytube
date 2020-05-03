<?php

namespace Tests\Unit;

use Artisan;
use App\ApiKey;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    protected $developmentKeys;
    protected $productionKeys;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->developmentKeys = factory(ApiKey::class, 2)->create([
            'environment' => ApiKey::LOCAL_ENV,
        ]);
        $this->productionKeys = factory(ApiKey::class, 3)->create([
            'environment' => ApiKey::PROD_ENV,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Artisan::call('config:clear');
    }

    public function testGetOneLocalIsRunningFile()
    {
        Config::set('APP_ENV', 'local');
        $this->assertTrue(
            in_array(
                Apikey::make()->getOne()->apikey,
                
            )
        );
    }

    public function testGetOneProductionIsRunningFile()
    {
        Config::set('APP_ENV', 'production');
        $this->assertTrue(
            in_array(
                Apikey::make()->getOne()->apikey,
                ApiKey::where('environment', '=', ApiKey::PROD_ENV)
                    ->get()
                    ->pluck('apikey')
                    ->toArray()
            )
        );
    }
}
