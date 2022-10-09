<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Jobs\SendWelcomeToPodmytubeEmailJob;
use App\Listeners\SendWelcomeToPodmytubeEmailListener;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendWelcomeToPodmytubeEmailListenerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Bus::fake();
    }

    /** @test */
    public function listener_should_dispatch(): void
    {
        $event = new Verified($this->user);

        $job = new SendWelcomeToPodmytubeEmailListener();
        $job->handle($event);
        Bus::assertDispatched(SendWelcomeToPodmytubeEmailJob::class, 1);
    }
}
