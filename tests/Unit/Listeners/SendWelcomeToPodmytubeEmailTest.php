<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Listeners\SendWelcomeToPodmytubeEmail;
use App\Mail\WelcomeToPodmytubeMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendWelcomeToPodmytubeEmailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake(SendWelcomeToPodmytubeEmail::class);
    }

    /** @test */
    public function when_not_verified_sending_welcome_to_podmytube_email_should_fail(): void
    {
        $nonVerifiedUser = User::factory()->create();
        $event = new Verified($nonVerifiedUser);

        $job = new SendWelcomeToPodmytubeEmail();
        $job->handle($event);
        Mail::assertNothingSent();
    }

    /** @test */
    public function sending_welcome_to_podmytube_email_should_succeed(): void
    {
        $verifiedUser = User::factory()->verifiedAt(now())->create();
        $event = new Verified($verifiedUser);

        $job = new SendWelcomeToPodmytubeEmail();
        $job->handle($event);
        Mail::assertSent(WelcomeToPodmytubeMail::class);
    }
}
