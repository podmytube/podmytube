<?php

namespace Tests\Feature;

use App\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Tests\TestCase;

class UpdateChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testCommandWithNoChannelShouldFail()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channel');
    }

    public function testShouldFailOnInvalidChannel()
    {
        $this->artisan('update:channel', ['channel_id' => 'invalid-channel-id'])->assertExitCode(1);
    }

    public function testShouldAddNewMediasOnValidChannelId()
    {
        $expectedNumberOfMedias = 2;
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->assertCount(0, $channel->medias);
        $this->artisan('update:channel', ['channel_id' => $channel->channel_id])
            ->assertExitCode(0);
        $channel->refresh();
        $this->assertCount($expectedNumberOfMedias, $channel->medias);
    }
}
