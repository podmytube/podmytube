<?php

namespace Tests\Unit;

use App\Channel;
use App\Services\ThumbService;
use Tests\TestCase;

class ThumbServiceTest extends TestCase
{
    /*
    public function testGetChannelThumbShouldWork()
    {
    $channel = Channel::find("freeChannel");
    $results = ThumbService::getChannelThumb($channel);
    dd($results);
    $this->assertTrue(false);
    }

    public function testGetThumbForChannelWithNoThumbShouldReturnDefaultOne()
    {
    $this->assertTrue(false);
    }

    public function testGetDefaultThumbShouldWork()
    {
    $this->assertTrue(false);
    }
     */

    /**
     * @expectedException Exception
     */
    public function testGetThumbForInvalidChannelShouldThrowAnException()
    {
        $channel = Channel::find("invalidChannel");
        $results = ThumbService::getChannelThumb($channel);
    }
}
