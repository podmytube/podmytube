<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Exceptions\ChannelAlreadyRegisteredException;
use App\Factories\CreateChannelFactory;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CreateChannelFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->seedCategories();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function from_youtube_url_should_succeed(): void
    {
        $youtubeUrl = 'https://www.youtube.com/channel/' . TestCase::PERSONAL_CHANNEL_ID;
        $channel = CreateChannelFactory::fromYoutubeUrl($this->user, $youtubeUrl);

        $this->assertNotNull($channel);
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals($this->user->user_id, $channel->user_id);
        $this->assertFalse($channel->active);
    }

    /** @test */
    public function from_youtube_url_and_active_should_succeed(): void
    {
        $youtubeUrl = 'https://www.youtube.com/channel/' . TestCase::PERSONAL_CHANNEL_ID;
        $channel = CreateChannelFactory::fromYoutubeUrl($this->user, $youtubeUrl, $active = true);

        $this->assertNotNull($channel);
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals($this->user->user_id, $channel->user_id);
        $this->assertTrue($channel->active);
    }

    /** @test */
    public function from_youtube_url_with_already_registered_channel_should_fail(): void
    {
        // channel exists
        Channel::factory()->create(['channel_id' => TestCase::PERSONAL_CHANNEL_ID]);

        // and I'm trying to add it once more
        $this->expectException(ChannelAlreadyRegisteredException::class);
        $youtubeUrl = 'https://www.youtube.com/channel/' . TestCase::PERSONAL_CHANNEL_ID;
        CreateChannelFactory::fromYoutubeUrl($this->user, $youtubeUrl);
    }
}
