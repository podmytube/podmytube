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
class AuthenticationAndLoginTest extends TestCase
{
    use RefreshDatabase;

    protected const RIGHT_PASSWORD = 'secret';

    /** @var \App\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function test_user_stay_guest_with_invalid_password(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_is_connected_with_right_password(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $this->user->email,
            'password' => self::RIGHT_PASSWORD,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs($this->user); // <===== this one is failing
    }

    public function testing_login_form(): void
    {
        $this->followingRedirects()
            ->get(route('login'))
            ->assertSuccessful()
            ->assertViewIs('auth.login')
        ;
    }

    public function test_auth_user_is_redirect_from_login_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('login'));
        $response->assertRedirect(route('home'));
    }

    public function testing_register_form(): void
    {
        $response = $this->get(route('register'));
        $response->assertSuccessful();
        $response->assertViewIs('auth.register');
    }

    public function test_auth_user_is_redirect_from_register_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('register'));
        $response->assertRedirect(route('home'));
    }

    public function test_user_with_correct_credentials_will_authenticate(): void
    {
        $response = $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => self::RIGHT_PASSWORD,
        ]);
        $response->assertRedirect(route('home'));
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_invalid_password_should_not_authenticate(): void
    {
        $response = $this->from('/')->post('/login', [
            'email' => $this->user->email,
            'password' => 'invalid-password',
        ]);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}
