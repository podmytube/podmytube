<?php

namespace Tests\Feature;

use App\Channel;
use App\Factories\ChannelCreationFactory;
use App\Plan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ChannelCreationFactoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\User $user */
    protected $user;

    /** @var string $myChannelId */
    protected $myChannelId = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    /** @todo check for ChannelRegistered Event */
    public function testCreationWithDefaultFreePlanShouldBeOk()
    {
        $validYoutubeUrl = "https://www.youtube.com/channel/{$this->myChannelId}?view_as=subscriber";
        $channelFactory = ChannelCreationFactory::create($this->user, $validYoutubeUrl);
        $this->assertInstanceOf(Channel::class, $channelFactory->channel());
        $this->assertEquals($this->myChannelId, $channelFactory->channel()->channel_id);
        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());
        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());
        $this->assertEquals(Plan::FREE_PLAN_ID, $channelFactory->channel()->subscription->id);
    }

    public function testCreationWithSpecificPlanShouldBeOk()
    {
        $this->markTestIncomplete("you should test with specific plan");
    }

    public function testCreationWithInvalidYoutubeChannelShouldThrowException()
    {
        $this->markTestIncomplete("to be done");
    }
}
