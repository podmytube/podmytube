<?php

namespace Tests\Feature;

use App\Channel;
use App\Events\ChannelRegistered;
use App\Exceptions\ChannelAlreadyRegisteredException;
use App\Exceptions\YoutubeChannelIdDoesNotExistException;
use App\Factories\ChannelCreationFactory;
use App\Plan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
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
        Artisan::call('db:seed');
    }

    /** @todo check for ChannelRegistered Event */
    public function testCreationWithDefaultFreePlanShouldBeOk()
    {
        Event::fake();
        $validYoutubeUrl = "https://www.youtube.com/channel/{$this->myChannelId}?view_as=subscriber";
        $channelFactory = ChannelCreationFactory::create($this->user, $validYoutubeUrl);

        $this->assertInstanceOf(Channel::class, $channelFactory->channel());
        $this->assertEquals($this->myChannelId, $channelFactory->channel()->channel_id);

        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());
        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());

        $this->assertEquals(Plan::FREE_PLAN_ID, $channelFactory->channel()->subscription->plan_id);

        Event::assertDispatched(ChannelRegistered::class);
    }

    public function testCreationWithSpecificPlanShouldBeOk()
    {
        Event::fake();

        $validYoutubeUrl = "https://www.youtube.com/channel/{$this->myChannelId}?view_as=subscriber";
        $weeklyYoutuberPlan = Plan::bySlug('weekly_youtuber');
        $channelFactory = ChannelCreationFactory::create($this->user, $validYoutubeUrl, $weeklyYoutuberPlan);

        $this->assertInstanceOf(Channel::class, $channelFactory->channel());
        $this->assertEquals($this->myChannelId, $channelFactory->channel()->channel_id);

        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());
        $this->assertEquals($this->user->id(), $channelFactory->channel()->user->id());

        $this->assertEquals(
            $weeklyYoutuberPlan->id,
            $channelFactory->channel()->subscription->plan_id
        );

        Event::assertDispatched(ChannelRegistered::class);
    }

    public function testCreationWithInvalidYoutubeChannelShouldThrowException()
    {
        $this->expectException(YoutubeChannelIdDoesNotExistException::class);
        ChannelCreationFactory::create($this->user, "https://www.youtube.com/channel/ThisChannelWillNeverExist?view_as=subscriber");
    }

    public function testTryingToRegisterSameChannelShouldThrowException()
    {
        factory(Channel::class)->create(['channel_id' => $this->myChannelId]);
        $this->expectException(ChannelAlreadyRegisteredException::class);
        ChannelCreationFactory::create($this->user, "https://www.youtube.com/channel/{$this->myChannelId}?view_as=subscriber");
    }
}
