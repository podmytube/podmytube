<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
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

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function who_want_newsletter_is_fine(): void
    {
        /** with no user */
        $result = User::whoWantNewsletter();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);

        /** with users that want newsletter and have active channel*/
        $nbExpectedUsersWhoWantNewsletter = 10;
        factory(User::class, $nbExpectedUsersWhoWantNewsletter)->create(['newsletter' => true])
            ->each(function (User $user): void {
                factory(Channel::class)->create(['user_id' => $user->userId()]);
            })
        ;
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

        // adding user that want newsletter but has inactive channel should not change the result either
        factory(User::class, 1)->create(['newsletter' => true])
            ->each(function (User $user): void {
                factory(Channel::class)->create(['user_id' => $user->userId(), 'active' => false]);
            })
        ;
        $result = User::whoWantNewsletter();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($nbExpectedUsersWhoWantNewsletter, $result);
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

    /** @test */
    public function dont_warn_user_for_exceeding_quota_should_be_good(): void
    {
        // user has not checked checkbox
        $this->user = factory(User::class)->create(['dont_warn_exceeding_quota' => false]);
        $this->assertTrue($this->user->wantToBeWarnedForExceedingQuota());

        // user has checked dont warn me for exceeding quota.
        $this->user = factory(User::class)->create(['dont_warn_exceeding_quota' => true]);
        $this->assertFalse($this->user->wantToBeWarnedForExceedingQuota());
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
