<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class AuthenticationAndLoginTest extends TestCase
{
    protected static $db_inited = false;
    protected static $rightPassword = "'i-love-laravel'";

    protected static $user;

    protected static function initUser()
    {
        self::$user = factory(User::class)->create([
            'password' => bcrypt(self::$rightPassword),
        ]);        
    }

    public function setUp(): void
    {
        parent::setUp();

        if (!self::$db_inited) {
            static::$db_inited = true;
            static::initUser();
        }
    }

    public function testUserStayGuestWithInvalidPassword()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => self::$user->email,
            'password' => 'invalid-password',
        ]);
        
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testUserIsConnectedWithRightPassword()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => self::$user->email,
            'password' => self::$rightPassword,
        ]);
        
        $response->assertRedirect('/home');
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs(self::$user); // <===== this one is failing
        /* $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest(); */
    }
/*
    public function testingLoginForm()
    {
        $response = $this->get('/login');
        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function testAuthUserIsRedirectFromLoginForm()
    {
        $response = $this->actingAs(self::$user)->get('/login');
        $response->assertRedirect('/home');
    }

    public function testingRegisterForm()
    {
        $response = $this->get('/register');
        $response->assertSuccessful();
        $response->assertViewIs('auth.register');
    }

    public function testAuthUserIsRedirectFromRegisterForm()
    {
        $response = $this->actingAs(self::$user)->get('/register');
        $response->assertRedirect('/home');
    }
    public function testUserWithCorrectCredentialsWillAuthenticate()
    {
        $response = $this->post(
            '/login',
            [
                'email' => self::$user->email,
                'password' => self::$rightPassword,
            ]
        );
        $response->assertRedirect(route('home'));
        $this->assertAuthenticated($guard = null);
        $this->assertAuthenticatedAs(self::$user);
    }

    public function testInvalidPasswordShouldNotAuthenticate()
    {
        $response = $this->from('/login')
            ->post(
                '/login',
                [
                    'email' => self::$user->email,
                    'password' => 'invalid-password',
                ]
            );

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
*/
}
