<?php

namespace Tests\Unit;

use App\Factories\RefreshVignetteFactory;
use App\Jobs\SendFileBySFTP;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RefreshVignetteFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Thumb $thumb */
    protected $thumb;

    public function setUp(): void
    {
        parent::setUp();
        $this->thumb = factory(Thumb::class)->create();
    }

    public function testfoo()
    {
        Bus::fake(SendFileBySFTP::class);
        $factory = RefreshVignetteFactory::init()->forThumb($this->thumb);

        $this->assertEquals(
            $this->channel->channel_id . '-' . config('app.feed_filename'),
            $factory->localFilename()
        );
        $this->assertEquals(
            '/app/tmp/' . $this->channel->channel_id . '-' . config('app.feed_filename'),
            $factory->localPath()
        );
        $this->assertEquals($this->channel->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
