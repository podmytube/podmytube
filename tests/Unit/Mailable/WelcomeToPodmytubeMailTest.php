<?php

declare(strict_types=1);

namespace Tests\Unit\Mailable;

use App\Mail\WelcomeToPodmytubeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class WelcomeToPodmytubeMailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function welcome_to_podmytube_email_is_fine(): void
    {
        $mailContent = new WelcomeToPodmytubeMail($this->user);

        $mailContent->assertSeeInOrderInHtml([
            'Welcome on Podmytube, ' . $this->user->firstname,
            "I'm delighted by your interest in my service !",
            'Now that you are registered, you should add the youtube channel you want to convert, in a magnificent podcast',
            'Convert my channel',
            'If you have any question, feel free to answer this email.',
            'Cheers.',
            'Fred',
        ]);
    }
}
