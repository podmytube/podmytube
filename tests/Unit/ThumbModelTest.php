<?php

namespace Tests\Unit;

use App\Thumb;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThumbModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Thumb $thumb */
    protected $thumb;

    public function setUp(): void
    {
        parent::setUp();
        $this->thumb = factory(Thumb::class)->create();
    }

    public function testingDefaultUrl()
    {
        $this->assertEquals(
            config('app.thumbs_url') . '/' . Thumb::DEFAULT_THUMB_FILE,
            Thumb::defaultUrl()
        );
    }

    public function testingRelativePath()
    {
        $this->assertEquals(
            $this->thumb->channel->channel_id . '/' . $this->thumb->file_name,
            $this->thumb->relativePath()
        );
    }

    public function testingPodcastUrl()
    {
        $this->assertEquals(
            config('app.thumbs_url') . '/' . $this->thumb->relativePath(),
            $this->thumb->podcastUrl()
        );
    }

    public function testingExistIsRunningFine()
    {
        /** factory is creating a true thumb file */
        $this->assertTrue($this->thumb->exists());

        /** newly born thumb is without */
        $thumb = new Thumb();
        $this->assertFalse($thumb->exists());
    }

    public function testRemotePath()
    {
        $this->assertEquals(
            config('app.thumbs_path') . $this->thumb->channel->channel_id . '/' . $this->thumb->file_name,
            $this->thumb->remoteFilePath()
        );
    }
}
