<?php

namespace Tests\Unit;

use Artisan;
use App\ApiKey;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    protected $developmentKeys;
    protected $productionKeys;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->developmentKeys = ApiKey::make()->developmentKeys()->toArray();
        $this->productionKeys = ApiKey::make()->productionKeys()->toArray();
    }

    public function testGetOneLocalIsRunningFile()
    {
        putenv('APP_ENV=local');
        $this->assertTrue(
            in_array(
                Apikey::make()->getOne()->apikey,
                $this->developmentKeys
            )
        );
    }

    public function testGetOneProductionIsRunningFile()
    {
        putenv('APP_ENV=production');
        $this->assertTrue(
            in_array(
                Apikey::make()->getOne()->apikey,
                $this->productionKeys
            )
        );
    }
}
