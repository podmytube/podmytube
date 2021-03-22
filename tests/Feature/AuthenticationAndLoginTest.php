<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndLoginTest extends TestCase
{
    use RefreshDatabase;

    protected $rightPassword = "'i-love-laravel'";

    /** @var \App\User $user */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['password' => bcrypt($this->rightPassword), ]);
    }

    public function testUserStayGuestWithInvalidPassword()
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

    public function testUserIsConnectedWithRightPassword()
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $this->user->email,
            'password' => $this->rightPassword,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs($this->user); // <===== this one is failing
    }

    public function testingLoginForm()
    {
        $this->followingRedirects()
            ->get(route('login'))
            ->assertSuccessful()
            ->assertViewIs('auth.login');
    }

    public function testAuthUserIsRedirectFromLoginForm()
    {
        $response = $this->actingAs($this->user)->get(route('login'));
        $response->assertRedirect(route('home'));
    }

    public function testingRegisterForm()
    {
        $response = $this->get(route('register'));
        $response->assertSuccessful();
        $response->assertViewIs('auth.register');
    }

    public function testAuthUserIsRedirectFromRegisterForm()
    {
        $response = $this->actingAs($this->user)->get(route('register'));
        $response->assertRedirect(route('home'));
    }

    public function testUserWithCorrectCredentialsWillAuthenticate()
    {
        $response = $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => $this->rightPassword,
        ]);
        $response->assertRedirect(route('home'));
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs($this->user);
    }

    public function testInvalidPasswordShouldNotAuthenticate()
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
