<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendMonthlyReportEmailJob;
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
class SendMonthlyReportEmailJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake(MonthlyReportMail::class);
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function sending_monthly_report_email_for_current_month_is_working_fine(): void
    {
        $wantedMonth = now()->startOfMonth();

        $job = new SendMonthlyReportEmailJob($this->channel, $wantedMonth);
        $job->handle();
        Mail::assertSent(fn (MonthlyReportMail $mail) => $mail->channel->youtube_id === $this->channel->youtube_id);
    }
}
