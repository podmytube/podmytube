<?php

namespace Tests\Unit;

use App\Channel;
use App\Playlist;
use App\Thumb;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ThumbModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Thumb $thumb */
    protected $thumb;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->thumb = factory(Thumb::class)->create();
        $this->channel = factory(Channel::class)->create();
        $this->playlist = factory(Playlist::class)->create();
    }

    public function tearDown(): void
    {
        if ($this->thumb->exists()) {
            $dirPath = dirname($this->thumb->relativePath());
            Storage::disk(Thumb::LOCAL_STORAGE_DISK)->deleteDirectory($dirPath);
        }
        parent::tearDown();
    }

    public function testingDefaultUrl()
    {
        $this->assertEquals(
            config('app.thumbs_url') . '/' . Thumb::DEFAULT_THUMB_FILE,
            Thumb::defaultUrl()
        );
    }

    /** @test */
    public function relative_path_for_channel_is_good()
    {
        $this->thumb->update([
            'coverable_type' => get_class($this->channel),
            'coverable_id' => $this->channel->id(),
        ]);
        $this->thumb->refresh();
        $this->assertEquals(
            $this->thumb->coverable->channelId() . '/' . $this->thumb->file_name,
            $this->thumb->relativePath()
        );
    }

    /** @test */
    public function set_coverable_is_good()
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertNotNull($this->thumb->coverable);
        $this->assertInstanceOf(Channel::class, $this->thumb->coverable);
        $this->assertEquals($this->channel->channelId(), $this->thumb->coverable->channelId());
    }

    public function testingRelativePath()
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            $this->thumb->coverable->channelId() . '/' . $this->thumb->file_name,
            $this->thumb->relativePath()
        );
    }

    public function testingPodcastUrl()
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            config('app.thumbs_url') . '/' . $this->thumb->relativePath(),
            $this->thumb->podcastUrl()
        );
    }

    /** @test */
    public function exists_is_running_fine()
    {
        $this->thumb->setCoverable($this->playlist);
        /** cover does not exist yet */
        $this->assertFalse($this->thumb->exists());

        /** creating one from fixture */
        $this->createFakeCoverFor($this->thumb);

        /** cover should exists now */
        $this->assertTrue($this->thumb->exists());
    }

    public function testRemotePath()
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            config('app.thumbs_path') . $this->thumb->coverable->channelId() . '/' . $this->thumb->file_name,
            $this->thumb->remoteFilePath()
        );
    }

    /** @test */
    public function coverable_label_is_fine()
    {
        $this->thumb->setCoverable($this->channel);
        $expectedLabel = get_class($this->channel) . "::find({$this->channel->id()})";
        $this->assertEquals($expectedLabel, $this->thumb->coverableLabel());
    }
}
