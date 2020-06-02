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

    protected $apikeys;

    public function setUp(): void
    {
        parent::setUp();
        // when only this test is run no problem.
        // however sometime RefreshDatabases does not clear table.
        // so I'm forcing the truncate to be made
        DB::table('api_keys')->delete();
        $this->apikeys = factory(ApiKey::class, 2)
            ->create()
            ->map(function ($apikey) {
                return $apikey->apikey;
            });
    }

    public function testGetOneLocalIsRunningFine()
    {
        $apikey = Apikey::make()->get();
        $this->assertTrue($this->apikeys->contains($apikey));
    }
}
