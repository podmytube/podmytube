<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelCreationIsPossible extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->seedApiKeys();
        $this->seedCategories();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function channel_creation_is_fine(): void
    {
        $expectedChannelId = 'UCw6bU9JT_Lihb2pbtqAUGQw';
        $this->followingRedirects()
            ->actingAs($this->user)
            ->from(route('channel.create'))
            ->post(route('channel.store'), [
                'channel_url' => 'https://www.youtube.com/channel/' . $expectedChannelId,
                'owner' => 1,
            ])
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSessionHasNoErrors()
            ->assertSeeText('has been successfully registered')
        ;
        $channel = Channel::byChannelId($expectedChannelId);
        $this->assertNotNull($channel);
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals($expectedChannelId, $channel->channelId());
    }

    /** @test */
    public function try_creating_a_non_existing_channel_id_will_fail(): void
    {
        array_map(
            function (string $invalidChannelId): void {
                $this->followingRedirects()
                    ->actingAs($this->user)
                    ->from(route('channel.create'))
                    ->post(route('channel.store'), [
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
