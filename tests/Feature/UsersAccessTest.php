<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UsersAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function simple_user_should_not_access_to_users(): void
    {
        $notSuperadmin = User::factory()->create();

        $this->actingAs($notSuperadmin)
            ->get(route('users.index'))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function channel_view_is_allowed_to_owner(): void
    {
        $superadmin = User::factory()->create(['superadmin' => true]);

        $this->actingAs($superadmin)
            ->get(route('users.index'))
            ->assertSuccessful()
            ->assertViewIs('users.index')
        ;
    }

    /** @test */
    public function simple_user_should_not_be_able_to_impersonate(): void
    {
        $notSuperadmin = User::factory()->create();
        $anotherUser = User::factory()->create();

        $this->actingAs($notSuperadmin)
            ->get(route('users.impersonate', $anotherUser))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function superadmin_should_be_able_to_impersonate(): void
    {
        $superadmin = User::factory()->create(['superadmin' => true]);
        $anotherUser = User::factory()->create();

        $this->followingRedirects()
            ->actingAs($superadmin)
            ->get(route('users.impersonate', $anotherUser))
            ->assertSuccessful()
            ->assertViewIs('home')
        ;

        $this->followingRedirects()
            ->actingAs($superadmin)
            ->get(route('users.leave-impersonate'))
            ->assertSuccessful()
            ->assertViewIs('home')
        ;
    }
}
