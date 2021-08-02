<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Auth\RegisterController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider invalid_firstname_provider
     * @dataProvider invalid_lastname_provider
     * @dataProvider invalid_email_provider
     * @dataProvider invalid_password_provider
     * @dataProvider invalid_terms_provider
     *
     * @param array  $formData   the form that will be posted
     * @param string $errorField the field that should be on error
     */
    public function invalid_data_shoud_fail(array $formData, string $errorField): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), $formData)
            ->assertSessionHasErrors([$errorField])
        ;
        $response->assertRedirect(route('register'));
    }

    /**
     * @test
     * @dataProvider valid_data_provider
     */
    public function valid_data_should_succeed(array $formData): void
    {
        $this->followingRedirects()
            ->post(route('register'), $formData)
            ->assertSessionHasNoErrors()
            ->assertSee([
                RegisterController::SUCCESS_MESSAGE,
            ])
        ;
        $userCreated = User::byEmail($formData['email']);
        $this->assertNotNull($userCreated);
        $this->assertInstanceOf(User::class, $userCreated);
        $this->assertEquals($formData['firstname'], $userCreated->firstname);
        $this->assertEquals($formData['lastname'], $userCreated->lastname);
    }

    /**
     * ===============================================
     * helpers & providers
     * ===============================================.
     */
    public function invalid_firstname_provider()
    {
        return [
            [['firstname' => null], 'firstname'],
        ];
    }

    public function invalid_lastname_provider()
    {
        return [
            [['lastname' => null], 'lastname'],
        ];
    }

    public function invalid_email_provider()
    {
        return [
            [['email' => null], 'email'], // email is required
            [['email' => 'not an email'], 'email'], // email is invalid
        ];
    }

    public function invalid_password_provider()
    {
        return [
            [['password' => null, 'password_confirmation' => null], 'password'], // email and confirmation are required
            [['password' => 'short', 'password_confirmation' => 'short'], 'password'], // password too short
            [['password' => 'passwordIs', 'password_confirmation' => 'mismatching'], 'password'], // password mismatch
        ];
    }

    public function invalid_terms_provider()
    {
        return [
            [['terms' => null], 'terms'], // owner checkbox required
        ];
    }

    public function valid_data_provider()
    {
        return [
            [[
                'firstname' => 'Gerard',
                'lastname' => 'Bouchard',
                'email' => 'gerard@bouchard.com',
                'password' => 'loremIpsum$',
                'password_confirmation' => 'loremIpsum$',
                'terms' => 1,
            ]],
            [[
                'firstname' => 'Geraldine',
                'lastname' => 'Lamy',
                'email' => 'geraldine@lamy.com',
                'password' => 'doloreSitAmet$',
                'password_confirmation' => 'doloreSitAmet$',
                'terms' => 1,
            ]],
        ];
    }
}
