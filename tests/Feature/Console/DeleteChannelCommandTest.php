<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Channel;
use App\Jobs\ChannelCleaningJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DeleteChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unknown_channel_should_fail(): void
    {
        $this->artisan('delete:channel', ['channel_id' => 'unknown_channel_id'])->assertExitCode(1);
    }

    /** @test */
    public function real_channel_id_should_be_deleted(): void
    {
        Bus::fake();

        $channel = Channel::factory()->create();

        // command should run properly
        $this->artisan('delete:channel', ['channel_id' => $channel->channelId()])->assertExitCode(0);

        Bus::assertDispatched(ChannelCleaningJob::class);
    }
}
