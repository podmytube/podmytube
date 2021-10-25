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
    public function by_email_is_doing_fine(): void
    {
        $expectedEmail = 'john@connor.com';
        $this->assertNull(User::byEmail($expectedEmail));

        factory(User::class)->create(['email' => $expectedEmail]);
        $user = User::byEmail($expectedEmail);
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($expectedEmail, $user->email);
    }

    /** @test */
    public function by_stripe_id_is_doing_fine(): void
    {
        $expectedStripeId = $this->faker->asciify('cus_************');
        $this->assertNull(User::byStripeId($expectedStripeId));

        factory(User::class)->create(['stripe_id' => $expectedStripeId]);
        $user = User::byStripeId($expectedStripeId);
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($expectedStripeId, $user->stripe_id);
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
