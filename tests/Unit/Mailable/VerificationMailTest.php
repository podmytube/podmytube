<?php

declare(strict_types=1);

namespace Tests\Unit\Mailable;

use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * This test is only testing the "rendering" of the template/view file
 * used by the notification system
 * ============================================================
 * take a look to >>> app/Providers/AuthServiceProvider.php <<<
 * ============================================================
 *
 * @coversNothing
 */
class VerificationMailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verification_email_template_is_fine(): void
    {
        $user = User::factory()->create();
        // creating faked verification url
        // real one will be given by MustVerifyEmail trait
        $expires = now()->addHour()->timestamp;
        $url = config('app.url') . '/email/verify/' . $user->user_id . 'lorem-ipsum?expires=' .
            $expires . '&signature=dolore-sit-amet'
        ;

        $mailContent = new VerificationMail($url);

        $mailContent->assertSeeInOrderInHtml([
            'Hello!',
            'Please click the button below to verify your email address.',
            '<a href="' . $url . '" class="button bgsuccess">',
            'If you did not create an account, no further action is required.',
            'If you have any question, feel free to answer this email.',
            'Cheers.',
            'Fred',
        ]);
    }
}
