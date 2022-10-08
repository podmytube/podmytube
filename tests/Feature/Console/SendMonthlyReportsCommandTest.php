<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Jobs\SendMonthlyReportEmailJob;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

/**
 * @internal
 *
 * @coversNothing
 */
class SendMonthlyReportsCommandTest extends CommandTestCase
{
    use RefreshDatabase;

    protected string $command = 'email:monthlyReport';

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake();
    }

    /** @test */
    public function with_no_active_channel_command_should_fail(): void
    {
        $this->artisan($this->command)->assertExitCode(1);
        Bus::assertNothingDispatched();
    }

    /** @test */
    public function with_one_active_channel_command_should_succeed(): void
    {
        $channel = Channel::factory()->active()->create();
        $this->artisan($this->command)->assertExitCode(0);
        Bus::assertDispatched(SendMonthlyReportEmailJob::class, 1);
        Bus::assertDispatched(
            fn (SendMonthlyReportEmailJob $job) => $job->podcastable->youtube_id === $channel->youtube_id
        );
    }

    /** @test */
    public function with_many_active_channel_command_should_succeed(): void
    {
        $expectedActiveChannelThatWillGetMonthlyReportEmail = 3;
        $channels = Channel::factory($expectedActiveChannelThatWillGetMonthlyReportEmail)->active()->create();
        $this->artisan($this->command)->assertExitCode(0);

        Bus::assertDispatched(SendMonthlyReportEmailJob::class, $expectedActiveChannelThatWillGetMonthlyReportEmail);
        $channels->each(
            fn (Channel $channel) => Bus::assertDispatched(fn (SendMonthlyReportEmailJob $job) => $job->podcastable->youtube_id === $channel->youtube_id)
        );
    }
}
