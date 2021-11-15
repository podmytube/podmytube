<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Channel;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UpdateChannelsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
    }

    public function test_no_active_channels_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channels')->assertExitCode(1);
    }

    public function test_should_add_new_medias_only_on_active_channels(): void
    {
        $expectedNumberOfMedias = 2;
        factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        factory(Channel::class)->create(['channel_id' => self::NOWTECH_CHANNEL_ID, 'active' => false]);
        $this->assertCount(0, Media::all());
        $this->artisan('update:channels')
            ->assertExitCode(0)
        ;
        $this->assertCount($expectedNumberOfMedias, Media::all());
    }
}
