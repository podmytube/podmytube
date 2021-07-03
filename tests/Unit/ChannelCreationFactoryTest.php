<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Category;
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

/**
 * @internal
 * @coversNothing
 */
class ChannelCreationFactoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\User */
    protected $user;

    /** @var \App\Category */
    protected $defaultCategory;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
        $this->defaultCategory = Category::bySlug(ChannelCreationFactory::DEFAULT_CATEGORY_SLUG);
    }

    /** @todo check for ChannelRegistered Event */
    public function test_creation_with_default_free_plan_should_be_ok(): void
    {
        Event::fake();
        $validYoutubeUrl = 'https://www.youtube.com/channel/'.self::PERSONAL_CHANNEL_ID.'?view_as=subscriber';
        $channelFactory = ChannelCreationFactory::create(
            $this->user,
            $validYoutubeUrl,
            Plan::bySlug('forever_free')
        );
        $channel = $channelFactory->channel();
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals(self::PERSONAL_CHANNEL_ID, $channel->channel_id);

        $this->assertEquals($this->user->id(), $channelFactory->user()->id());

        $this->assertEquals(Plan::FREE_PLAN_ID, $channel->subscription->plan_id);
        $this->assertEquals(
            $this->defaultCategory->id,
            $channel->category_id,
            "Channel should have default category {$this->defaultCategory->name} and have {$channel->category->name}"
        );
        Event::assertDispatched(ChannelRegistered::class);
    }

    public function test_creation_with_specific_plan_should_be_ok(): void
    {
        Event::fake();

        $validYoutubeUrl = 'https://www.youtube.com/channel/'.self::PERSONAL_CHANNEL_ID.'?view_as=subscriber';
        $weeklyYoutuberPlan = Plan::bySlug('weekly_youtuber');
        $channelFactory = ChannelCreationFactory::create(
            $this->user,
            $validYoutubeUrl,
            $weeklyYoutuberPlan
        );

        $this->assertInstanceOf(Channel::class, $channelFactory->channel());
        $this->assertEquals(self::PERSONAL_CHANNEL_ID, $channelFactory->channel()->channel_id);

        $this->assertEquals($this->user->id(), $channelFactory->user()->id());

        $this->assertEquals(
            $weeklyYoutuberPlan->id,
            $channelFactory->channel()->subscription->plan_id
        );

        Event::assertDispatched(ChannelRegistered::class);
    }

    public function test_creation_with_invalid_youtube_channel_should_throw_exception(): void
    {
        $this->expectException(YoutubeChannelIdDoesNotExistException::class);
        ChannelCreationFactory::create(
            $this->user,
            'https://www.youtube.com/channel/ThisChannelWillNeverExist?view_as=subscriber',
            Plan::bySlug('forever_free')
        );
    }

    public function test_trying_to_register_same_channel_should_throw_exception(): void
    {
        factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->expectException(ChannelAlreadyRegisteredException::class);
        ChannelCreationFactory::create(
            $this->user,
            'https://www.youtube.com/channel/'.self::PERSONAL_CHANNEL_ID.'?view_as=subscriber',
            Plan::bySlug('forever_free')
        );
    }
}
