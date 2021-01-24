<?php

namespace Tests\Feature;

use App\Channel;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Tests\TestCase;

class UpdateChannelsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testNoActiveChannelsShouldThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('update:channels');
    }

    public function testShouldAddNewMediasOnlyOnActiveChannels()
    {
        $expectedNumberOfMedias = 2;
        factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        factory(Channel::class)->create(['channel_id' => self::NOWTECH_CHANNEL_ID, 'active' => false]);
        $this->assertCount(0, Media::all());
        $this->artisan('update:channels')
            ->assertExitCode(0);
        $this->assertCount($expectedNumberOfMedias, Media::all());
    }
}
