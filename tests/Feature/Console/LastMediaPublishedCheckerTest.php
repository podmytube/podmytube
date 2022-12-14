<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Exceptions\NoActiveChannelException;
use App\Mail\ChannelIsInTroubleWarningMail;
use App\Models\Media;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

/**
 * @internal
 *
 * @coversNothing
 */
class LastMediaPublishedCheckerTest extends CommandTestCase
{
    use RefreshDatabase;

    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->starterPlan = Plan::factory()->name('starter')->create();
        Mail::fake();
    }

    /** @test */
    public function check_lastmedia_with_no_paying_channel_should_fail(): void
    {
        $this->expectException(NoActiveChannelException::class);
        $this->artisan('check:lastmedia')->assertExitCode(1);
        // Assert that no mailables were sent...
        Mail::assertNothingQueued();
    }

    /** @test */
    public function check_lastmedia_with_paying_channel_and_no_grabbed_medias_should_send_mail(): void
    {
        $this->seedApiKeys();
        $this->createMyOwnChannel($this->starterPlan);
        $this->artisan('check:lastmedia')->assertExitCode(0);
        Mail::assertQueued(ChannelIsInTroubleWarningMail::class);
    }

    /** @test */
    public function check_lastmedia_with_paying_channel_and_all_medias_grabbed_should_send_nothing(): void
    {
        $this->seedApiKeys();
        $channel = $this->createMyOwnChannel($this->starterPlan);
        // creating media
        Media::factory()
            ->grabbedAt(now())
            ->create([
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
                'channel_id' => $channel->channel_id,
            ])
        ;
        $this->artisan('check:lastmedia')->assertExitCode(0);
        Mail::assertNothingQueued();
    }
}
