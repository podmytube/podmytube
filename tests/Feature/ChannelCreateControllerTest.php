<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\IsFakingYoutube;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelCreateControllerTest extends TestCase
{
    use IsFakingYoutube;
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedStripePlans();
        $this->seedApiKeys();
        $this->seedCategories();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function step1_validated_should_get_you_to_step2(): void
    {
        $expectedChannelId = 'UCw6bU9JT_Lihb2pbtqAUGQw';
        $this->fakeChannelResponse($expectedChannelId);
        $this->followingRedirects()
            ->actingAs($this->user)
            ->from(route('channel.step1'))
            ->post(route('channel.step1.validate'), [
                'channel_url' => 'https://www.youtube.com/channel/' . $expectedChannelId,
                'owner' => 1,
            ])
            ->assertSuccessful()
            ->assertViewIs('channel.step2')
            ->assertSessionHasNoErrors()
        ;
        $channel = Channel::byChannelId($expectedChannelId);
        $this->assertNotNull($channel);
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals($expectedChannelId, $channel->channelId());

        // free subscription should have been set
        $this->assertNotNull($channel->subscription);
        $this->assertInstanceOf(Subscription::class, $channel->subscription);
        $this->assertEquals($this->getFreePlan()->id, $channel->subscription->plan_id);
    }

    /** @test */
    public function try_creating_a_non_existing_channel_id_will_fail(): void
    {
        array_map(
            function (string $invalidChannelId): void {
                $this->fakeEmptyChannelResponse($invalidChannelId);
                $this->followingRedirects()
                    ->actingAs($this->user)
                    ->from(route('channel.step1'))
                    ->post(route('channel.step1.validate'), [
                        'channel_url' => "https://www.youtube.com/channel/{$invalidChannelId}",
                        'owner' => 1,
                    ])
                    ->assertViewIs('channel.create')
                ;
                $this->assertNull(Channel::byChannelId($invalidChannelId));
            },
            [
                'This-Will^never~exist',
                'invalidChannelId',
            ]
        );
    }
}
