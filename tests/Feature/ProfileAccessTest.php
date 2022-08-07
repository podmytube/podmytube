<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\RemoveAccountJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ProfileAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guest_should_be_denied(): void
    {
        $this->get(route('user.index'))
            ->assertRedirect('/login')
        ;
    }

    /** @test */
    public function user_should_access_his_profile(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('user.index'))
            ->assertViewIs('user.edit')
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function user_should_be_able_to_update_profile(): void
    {
        $expectedFirstname = 'Obiwan';
        $expectedLastname = 'Kenobi';

        $this->followingRedirects()
            ->actingAs($this->user)
            ->patch(route('user.update', $this->user), [
                'firstname' => $expectedFirstname,
                'lastname' => $expectedLastname,
                'email' => 'valid@example.com',
                'newsletter' => '1',
            ])
            ->assertSuccessful()
            ->assertViewIs('home')
        ;
        $user = User::byEmail('valid@example.com');
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($expectedFirstname, $user->firstname);
        $this->assertEquals($expectedLastname, $user->lastname);
        $this->assertTrue($user->newsletter);
    }

    /** @test */
    public function user_should_be_able_to_unsubscribe_newsletter(): void
    {
        $expectedFirstname = 'Obiwan';
        $expectedLastname = 'Kenobi';

        $this->followingRedirects()
            ->actingAs($this->user)
            ->patch(route('user.update', $this->user), [
                'firstname' => $expectedFirstname,
                'lastname' => $expectedLastname,
                'email' => 'valid@example.com',
                'newsletter' => 0,
            ])
            ->assertSuccessful()
            ->assertViewIs('home')
        ;
        $user = User::byEmail('valid@example.com');
        $this->assertNotNull($user);
        $this->assertFalse($user->newsletter);
    }

    /** @test */
    public function user_should_denied_other_profile_update(): void
    {
        $anotherUser = User::factory()->create();
        $this->followingRedirects()
            ->actingAs($anotherUser)
            ->patch(route('user.update', $this->user), [
                'firstname' => 'Another',
                'lastname' => 'Person',
                'email' => 'valid@example.com',
                'newsletter' => '1',
            ])
            ->assertForbidden()
        ;
    }

    /** @test */
    public function user_who_want_to_be_deleted_is_really_deleted(): void
    {
        Bus::fake();
        $this->followingRedirects()
            ->actingAs($this->user)
            ->delete(route('user.destroy', $this->user))
            ->assertSuccessful()
        ;
        // user should have been logged out
        $this->assertGuest();

        // media clening should have been dispatched twice.
        Bus::assertDispatched(RemoveAccountJob::class, 1);
    }
}
