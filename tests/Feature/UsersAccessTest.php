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
        $notSuperadmin = factory(User::class)->create();

        $this->actingAs($notSuperadmin)
            ->get(route('users.index'))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function channel_view_is_allowed_to_owner(): void
    {
        $superadmin = factory(User::class)->create(['superadmin' => true]);

        $this->actingAs($superadmin)
            ->get(route('users.index'))
            ->assertSuccessful()
            ->assertViewIs('users.index')
        ;
    }

    /** @test */
    public function simple_user_should_not_be_able_to_impersonate(): void
    {
        $notSuperadmin = factory(User::class)->create();
        $anotherUser = factory(User::class)->create();

        $this->actingAs($notSuperadmin)
            ->get(route('users.impersonate', $anotherUser))
            ->assertForbidden()
        ;

        $this->actingAs($notSuperadmin)
            ->get(route('users.leave-impersonate'))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function superadmin_should_be_able_to_impersonate(): void
    {
        $superadmin = factory(User::class)->create(['superadmin' => true]);
        $anotherUser = factory(User::class)->create();

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
