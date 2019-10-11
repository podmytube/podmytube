<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelCreationIsPossible extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testChannelCreateIsOk()
    {
        $response = $this->get('/channel/create');
        $response->assertStatus(200);
    }    
}
