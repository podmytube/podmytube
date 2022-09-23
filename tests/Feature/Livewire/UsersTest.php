<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Users;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UsersTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    public function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['superadmin' => true]);
    }

    /** @test */
    public function not_allowed_user_should_be_denied(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        Livewire::test(Users::class)
            ->assertForbidden()
        ;
    }

    /** @test */
    public function superadmin_should_be_allowed(): void
    {
        $this->actingAs($this->superadmin);
        Livewire::test(Users::class)
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function no_active_channel_should_display_nothing(): void
    {
        Channel::factory(5)->create([
            'active' => 0,
        ]);

        $this->actingAs($this->superadmin);
        Livewire::test(Users::class)
            ->assertSuccessful()
            ->assertSee('')

        ;
    }

    /** @test */
    public function active_channel_should_be_displayed(): void
    {
        $inactiveChannels = Channel::factory(5)->create(['active' => 0]);

        $channels = Channel::factory(3)->create(['active' => 1]);

        $this->actingAs($this->superadmin);
        $livewire = Livewire::test(Users::class)
            ->assertSuccessful()
            ->assertSeeTextInOrder([
                'User',
                'Channel',
                'Action',
            ])
        ;

        $channels->each(fn (Channel $channel) => $livewire->assertSeeTextInOrder([
            $channel->user->name,
            $channel->channel_name,
        ]));

        $inactiveChannels->each(function (Channel $channel) use ($livewire): void {
            $livewire->assertDontSeeText($channel->user->name);
            $livewire->assertDontSeeText($channel->channel_name);
        });
    }
}
