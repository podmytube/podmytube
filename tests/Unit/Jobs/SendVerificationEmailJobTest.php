<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * @internal
 *
 * Verification mail is sent through a notification
 * implemented in trait vendor/.../Illuminate/Auth/MustVerifyEmail.php
 * the function sendEmailVerificationNotification() is only doing
 * $this->notify(new VerifyEmail);
 * VerifyEmail is a notification (not a mailable)
 * I "overloaded" the simpleMessage view with an addon into
 * app/Providers/AuthServiceProvider.php
 * VerifyEmail::toMailUsing(function ($notifiable, $url) {
 *      return (new MailMessage())
 *          ->view('emails.verification', ['url' => $url])
 * the only trick here is that I'm using one view with my layout.
 *
 * @coversNothing
 */
class SendVerificationEmailJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function sending_verification_email_is_working_fine(): void
    {
        $user = User::factory()->create();
        $job = new SendVerificationEmailJob($user);
        $job->handle();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function user_already_verified_should_not_being_sent_verification_email(): void
    {
        $user = User::factory()->verifiedAt(now()->subWeek())->create();
        $job = new SendVerificationEmailJob($user);
        $job->handle();

        Notification::assertNothingSent();
    }
}
