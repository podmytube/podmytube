<?php

namespace Tests\Unit;

use App\User;
use App\Channel;
use App\Services\ChannelService;
use Tests\TestCase;

class ChannelServiceTest extends TestCase
{
    protected $fakeUser = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testOnlyTheOwnerShouldSeeHisChannels()
    {
        $this->markTestIncomplete();
    }

    public function testUserWithNoChannelShouldntSeeAny()
    {
        $this->markTestIncomplete();
    }
}
