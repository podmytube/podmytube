<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function who_want_newsletter_is_fine(): void
    {
        /** with no user */
        $result = User::whoWantNewsletter();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);

        /** with user that want newsletter */
        $nbExpectedUsersWhoWantNewsletter = 10;
        $usersThatWillReceiveNewsletter = factory(User::class, $nbExpectedUsersWhoWantNewsletter)->create(['newsletter' => true]);
        $result = User::whoWantNewsletter();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($nbExpectedUsersWhoWantNewsletter, $result);
        $this->checkRowResult($result);

        // adding users that dont want newsletter does not change the result
        factory(User::class, 3)->create(['newsletter' => false]);
        $result = User::whoWantNewsletter();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($nbExpectedUsersWhoWantNewsletter, $result);
        $this->assertEquals($nbExpectedUsersWhoWantNewsletter + 3, User::count());
    }

    /** @test */
    public function is_superadmin_should_be_good(): void
    {
        $user = factory(User::class)->create();
        $this->assertFalse($user->isSuperAdmin());

        $superadmin = factory(User::class)->create(['superadmin' => true]);
        $this->assertTrue($superadmin->isSuperAdmin());
    }

    /** @test */
    public function name_attribute_should_be_good(): void
    {
        $user = factory(User::class)->create();
        $this->assertEquals($user->firstname . ' ' . $user->lastname, $user->name);

        $user = factory(User::class)->create(['lastname' => null]);
        $this->assertEquals($user->firstname, $user->name);

        // firstname cannot be null (DB constraint)
    }

    /**
     * ===============================================
     * helpers & providers
     * ===============================================.
     */
    public function checkRowResult(Collection $users): void
    {
        $users->map(function (User $user): void {
            $this->assertNotNull($user->email);
            $this->assertNotNull($user->firstname);
            $this->assertNotNull($user->lastname);
        });
    }
}
