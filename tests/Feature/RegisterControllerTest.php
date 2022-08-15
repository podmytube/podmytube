<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
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
            'firstname is required' => [['firstname' => null], 'firstname'],
        ];
    }

    public function invalid_lastname_provider()
    {
        return [
            'lastname is required' => [['lastname' => null], 'lastname'],
        ];
    }

    public function invalid_email_provider()
    {
        return [
            'email is required' => [['email' => null], 'email'], // email is required
            'email must be valid' => [['email' => 'not an email'], 'email'], // email is invalid
        ];
    }

    public function invalid_password_provider()
    {
        return [
            'email and confirmation are required' => [['password' => null, 'password_confirmation' => null], 'password'],
            'password too short' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
            'password mismatch' => [['password' => 'passwordIs', 'password_confirmation' => 'mismatching'], 'password'],
        ];
    }

    public function invalid_terms_provider()
    {
        return [
            'owner checkbos is required' => [['terms' => null], 'terms'], // owner checkbox required
        ];
    }

    public function valid_data_provider()
    {
        return [
            'gerard bouchard should register properly' => [[
                'firstname' => 'Gerard',
                'lastname' => 'Bouchard',
                'email' => 'gerard@bouchard.com',
                'password' => 'loremIpsum$',
                'password_confirmation' => 'loremIpsum$',
                'terms' => 1,
            ]],
            'geraldine lamy should register properly' => [[
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
