<?php

namespace Tests\Unit;

use App\ApiKey;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyModelTest extends TestCase
{
    use RefreshDatabase;

    protected $apikeys;


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
}
