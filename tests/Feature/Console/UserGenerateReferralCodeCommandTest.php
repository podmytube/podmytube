<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserGenerateReferralCodeCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function user_with_no_referral_code_should_have_one(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'referral_code' => null,
        ]);
        $this->assertNull($user->referral_code);
        $this->artisan('user:generate-referral-code')->assertExitCode(0)
        ;
        $user->refresh();
        $this->assertNotNull($user->referral_code);
    }
}
