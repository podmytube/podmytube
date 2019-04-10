<?php

namespace Tests\Unit;
use App\User;
use App\Channel;
use App\Services\ChannelService;
use Tests\TestCase;

class ChannelServiceTest extends TestCase
{
    protected $fakeUser = null;

    protected function setUp():void 
    {
        parent::setUp();
        /**
         * Creating fake user
         */
        $this->fakeUser = factory(User::class)->create();
    }

    protected function tearDown():void 
    {
        parent::tearDown();
        /**
         * deleting fake user
         */
        $this->fakeUser->delete();
    }

    public function testOnlyTheOwnerShouldSeeHisChannels()
    {
        $expectedChannelIds = [
            'freeChannel',
            'earlyChannel',
            'weeklyChannel',
            'dailyChannel',
        ];

        $user = User::find(1);
        $userChannels = ChannelService::getAuthenticatedUserChannels($user);
        $obtainedChannelsIds = $userChannels->pluck('channel_id')->toArray();

        $this->assertEqualsCanonicalizing(
            $expectedChannelIds,
            $obtainedChannelsIds,
            "Channels ids for owner 1 should be {".implode(', ', $expectedChannelIds)."} and we received {".implode(', ', $obtainedChannelsIds)."}");
    }

    public function testUserWithNoChannelShouldntSeeAny() 
    {
        $this->expectException(\Exception::class);
        $userChannels = ChannelService::getAuthenticatedUserChannels($this->fakeUser);
    }

}
