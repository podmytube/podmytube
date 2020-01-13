<?php

namespace Tests\Unit;

use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicCheckingsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertEquals(env("APP_ENV"), "testing");
        $this->assertTrue(env("APP_DEBUG"), "DEBUG in .env.testing should be set to true");
        $this->assertEquals(env("DB_DATABASE"), ":memory:");                       
    }
}
