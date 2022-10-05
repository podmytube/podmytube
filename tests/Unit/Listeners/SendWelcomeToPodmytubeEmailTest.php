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
        $this->user = User::factory()->create();
        $this->event = new Verified($this->user);

        $job = new SendWelcomeToPodmytubeEmail();
        $job->handle($this->event);
        Mail::assertNothingSent();
    }

    /** @test */
    public function sending_welcome_to_podmytube_email_should_succeed(): void
    {
        $this->user = User::factory()->verifiedAt(now())->create();
        $this->event = new Verified($this->user);

        $job = new SendWelcomeToPodmytubeEmail();
        $job->handle($this->event);
        Mail::assertSent(WelcomeToPodmytubeMail::class);
    }
}
