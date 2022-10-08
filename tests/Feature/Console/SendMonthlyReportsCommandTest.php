<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Mail\MonthlyReportMail;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendMonthlyReportsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected string $command = 'email:monthlyReport';

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function with_no_active_channel_command_should_fail(): void
    {
        $this->artisan($this->command)->assertExitCode(1);
        Mail::assertNothingQueued();
    }

    /** @test */
    public function with_one_active_channel_command_should_succeed(): void
    {
        $channel = Channel::factory()->active()->create();
        $this->artisan($this->command)->assertExitCode(0);
        Mail::assertQueued(function (MonthlyReportMail $mail) use ($channel) {
            return $mail->channel->youtube_id === $channel->youtube_id;
        });
    }

    /** @test */
    public function with_many_active_channel_command_should_succeed(): void
    {
        $expectedActiveChannelThatWillGetMonthlyReportEmail = 3;
        $channels = Channel::factory($expectedActiveChannelThatWillGetMonthlyReportEmail)->active()->create();
        $this->artisan($this->command)->assertExitCode(0);

        Mail::assertQueued(MonthlyReportMail::class, $expectedActiveChannelThatWillGetMonthlyReportEmail);
        $channels->each(
            fn (Channel $channel) => Mail::assertQueued(fn (MonthlyReportMail $mail) => $mail->channel->youtube_id === $channel->youtube_id)
        );
    }
}
