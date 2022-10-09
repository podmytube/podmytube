<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Mail\ChannelIsRegisteredMail;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendChannelIsRegisteredEmailJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake(ChannelIsRegisteredMail::class);
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function sending_channel_is_registered_mail_is_fine(): void
    {
        $job = new SendChannelIsRegisteredEmailJob($this->channel);
        $job->handle();
        Mail::assertSent(fn (ChannelIsRegisteredMail $mail) => $mail->channel->youtube_id === $this->channel->youtube_id);
    }
}
