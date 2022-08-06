<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Channel;
use App\Jobs\ChannelHasReachedItsLimitsJob;
use App\Mail\ChannelHasReachedItsLimitsMail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelHasReachedItsLimitsJobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function exceeding_quota_mail_should_be_sent(): void
    {
        // user HAS NOT checked it
        $this->user = User::factory()->create(['dont_warn_exceeding_quota' => false]);
        $this->channel = Channel::factory()->create(['user_id' => $this->user->id()]);

        $job = new ChannelHasReachedItsLimitsJob($this->channel);
        $job->handle();

        Mail::assertQueued(ChannelHasReachedItsLimitsMail::class);
    }

    /** @test */
    public function exceeding_quota_mail_should__not_be_sent(): void
    {
        $this->user = User::factory()->create(['dont_warn_exceeding_quota' => true]);
        $this->channel = Channel::factory()->create(['user_id' => $this->user->id()]);

        // running podcast deletion
        $job = new ChannelHasReachedItsLimitsJob($this->channel);
        $job->handle();

        Mail::assertNothingQueued();
    }
}
