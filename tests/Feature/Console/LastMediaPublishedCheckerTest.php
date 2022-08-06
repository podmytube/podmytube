<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Exceptions\NoPayingChannelException;
use App\Mail\ChannelIsInTroubleWarningMail;
use App\Media;
use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LastMediaPublishedCheckerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function check_lastmedia_with_no_paying_channel_should_fail(): void
    {
        $this->expectException(NoPayingChannelException::class);
        $this->artisan('check:lastmedia')->assertExitCode(1);
        // Assert that no mailables were sent...
        Mail::assertNothingQueued();
    }

    /** @test */
    public function check_lastmedia_with_paying_channel_and_no_grabbed_medias_should_send_mail(): void
    {
        $this->seedApiKeys();
        $this->seedPlans();
        $this->createMyOwnChannel(Plan::bySlug('starter'));
        $this->artisan('check:lastmedia')->assertExitCode(0);
        Mail::assertQueued(ChannelIsInTroubleWarningMail::class);
    }

    /** @test */
    public function check_lastmedia_with_paying_channel_and_all_medias_grabbed_should_send_nothing(): void
    {
        $this->seedApiKeys();
        $this->seedPlans();
        $channel = $this->createMyOwnChannel(Plan::bySlug('starter'));
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
