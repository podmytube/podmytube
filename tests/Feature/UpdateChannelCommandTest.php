<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UpdateChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }

    public function test_command_with_no_channel_should_fail(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channel');
    }

    public function test_should_fail_on_invalid_channel(): void
    {
        $this->artisan('update:channel', ['channel_id' => 'invalid-channel-id'])->assertExitCode(1);
    }

    public function test_should_add_new_medias_on_valid_channel_id(): void
    {
        $expectedNumberOfMedias = 2;
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->assertCount(0, $channel->medias);
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0)
        ;
        $channel->refresh();
        $this->assertCount($expectedNumberOfMedias, $channel->medias);
    }
}
