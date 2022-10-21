<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendNewReferralEmailJob;
use App\Mail\YouHaveNewReferralMail;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
class SendNewReferralEmailJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake(YouHaveNewReferralMail::class);
    }

    /** @test */
    public function sending_new_referral_email_is_working_fine(): void
    {
        $starterPlan = Plan::factory()->name('starter')->create();
        $referrer = User::factory()->verifiedAt(now()->subMonth())->create();
        $referral = User::factory()->verifiedAt(now())->withReferrer($referrer)->create();
        $channel = $this->createChannel($referral, $starterPlan);
        $job = new SendNewReferralEmailJob($channel);
        $job->handle();

        Mail::assertSent(YouHaveNewReferralMail::class, 1);
        Mail::assertSent(fn (YouHaveNewReferralMail $mail) => $mail->referrer->email === $referrer->email);
    }
}
