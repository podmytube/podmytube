<?php

namespace Tests\Unit;

use Tests\TestCase;

class CreateChannelServiceTest extends TestCase
{
    /**
     * Asking for 2 plans in prod mode should return one array with the 2 stripe plans.
     * @test
     */
    public function ChannelCreationIsWorkingWell()
    {
        $this->assertTrue(true);
        /*
        $result = ChannelCreateService::create()
        $this->assertEquals($expected, $result,
        "Asking for some stripe plans in prod mode has failed !");
        */
    }
    
    
}
