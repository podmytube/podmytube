<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\ChannelCleaningJob;
use App\Jobs\RemoveAccountJob;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RemoveAccountJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channelToDelete;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);
        Bus::fake();
    }

    /** @test */
    public function removing_account_is_working_fine(): void
    {
        $user = factory(User::class)->create();
        $plan = $this->getFreePlan();

        $this->createChannel($user, $plan);

        // dispatching media deletion
        $job = new RemoveAccountJob($user);
        $job->handle();

        // media clening should have been dispatched twice.
        Bus::assertDispatched(ChannelCleaningJob::class, 1);

        // user should have been deleted from DB.
        $this->assertNull(User::byEmail($user->email));
    }
}
