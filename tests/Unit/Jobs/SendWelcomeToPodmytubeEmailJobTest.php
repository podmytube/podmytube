<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendWelcomeToPodmytubeEmailJob;
use App\Mail\WelcomeToPodmytubeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendWelcomeToPodmytubeEmailJobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake(WelcomeToPodmytubeMail::class);
    }

    /** @test */
    public function when_not_verified_sending_welcome_to_podmytube_email_should_fail(): void
    {
        $nonVerifiedUser = User::factory()->create();

        $job = new SendWelcomeToPodmytubeEmailJob($nonVerifiedUser);
        $job->handle();
        Mail::assertNothingSent();
    }

    /** @test */
    public function sending_welcome_to_podmytube_email_should_succeed(): void
    {
        $verifiedUser = User::factory()->verifiedAt(now())->create();

        $job = new SendWelcomeToPodmytubeEmailJob($verifiedUser);
        $job->handle();
        Mail::assertSent(WelcomeToPodmytubeMail::class, 1);
        Mail::assertSent(fn (WelcomeToPodmytubeMail $mail) => $mail->user->email === $verifiedUser->email);
    }
}
