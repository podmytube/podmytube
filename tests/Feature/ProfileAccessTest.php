<?php

namespace Tests\Feature;

use App\User;
use Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User $user */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
        $this->user = factory(User::class)->create();
    }

    public function testUserShouldAccessProfile()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('user.show', $this->user))
            ->assertSuccessful()
            ->assertViewIs('user.show');
    }

    public function testUserShouldDeniedOtherProfile()
    {
        $anotherUser = factory(User::class)->create();
        $this->followingRedirects()
            ->actingAs($anotherUser)
            ->get(route('user.show', $this->user))
            ->assertForbidden();
    }

    public function testUserShouldAccessProfileEdit()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('user.edit', $this->user))
            ->assertSuccessful()
            ->assertViewIs('user.edit');
    }

    public function testUserShouldDeniedOtherProfileEdit()
    {
        $anotherUser = factory(User::class)->create();
        $this->followingRedirects()
            ->actingAs($anotherUser)
            ->get(route('user.edit', $this->user))
            ->assertForbidden();
    }

    public function testUserShouldAccessProfileUpdate()
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->patch(route('user.update', $this->user), [
                'name' => 'Another name',
                'email' => 'valid@example.com',
                'language' => 'fr',
                'newsletter' => '1',
            ])
            ->assertSuccessful()
            ->assertViewIs('user.show')
            ->assertSeeText('Another name');
    }

    public function testUserShouldDeniedOtherProfileUpdate()
    {
        $anotherUser = factory(User::class)->create();
        $this->followingRedirects()
            ->actingAs($anotherUser)
            ->patch(route('user.update', $this->user), [
                'name' => 'Another name',
                'email' => 'valid@example.com',
                'language' => 'fr',
                'newsletter' => '1',
            ])
            ->assertForbidden();
    }
}
