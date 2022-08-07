<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ThumbModelTest extends TestCase
{
    use RefreshDatabase;

    protected Thumb $thumb;
    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->thumb = Thumb::factory()->create();
        $this->channel = Channel::factory()->create();
        $this->playlist = Playlist::factory()->create();
    }

    public function tearDown(): void
    {
        if ($this->thumb->exists()) {
            $dirPath = dirname($this->thumb->relativePath());
            Storage::disk(Thumb::LOCAL_STORAGE_DISK)->deleteDirectory($dirPath);
        }
        parent::tearDown();
    }

    /** @test */
    public function default_url_should_be_good(): void
    {
        $this->assertEquals(
            config('app.thumbs_url') . '/' . Thumb::DEFAULT_THUMB_FILE,
            Thumb::defaultUrl()
        );
    }

    /** @test */
    public function relative_path_for_channel_is_good(): void
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
    public function set_coverable_is_good(): void
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertNotNull($this->thumb->coverable);
        $this->assertInstanceOf(Channel::class, $this->thumb->coverable);
        $this->assertEquals($this->channel->channelId(), $this->thumb->coverable->channelId());
    }

    /** @test */
    public function relative_path_is_good(): void
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            $this->thumb->coverable->channelId() . '/' . $this->thumb->file_name,
            $this->thumb->relativePath()
        );
    }

    /** @test */
    public function podcast_url_is_good(): void
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            config('app.thumbs_url') . '/' . $this->thumb->relativePath(),
            $this->thumb->podcastUrl()
        );
    }

    /** @test */
    public function exists_is_running_fine(): void
    {
        $this->thumb->setCoverable($this->playlist);
        // cover does not exist yet
        $this->assertFalse($this->thumb->exists());

        // creating one from fixture
        $this->createFakeCoverFor($this->thumb);

        // cover should exists now
        $this->assertTrue($this->thumb->exists());
    }

    public function test_remote_path(): void
    {
        $this->thumb->setCoverable($this->channel);
        $this->assertEquals(
            config('app.thumbs_path') . $this->thumb->coverable->channelId() . '/' . $this->thumb->file_name,
            $this->thumb->remoteFilePath()
        );
    }

    /** @test */
    public function coverable_label_is_fine(): void
    {
        $this->thumb->setCoverable($this->channel);
        $expectedLabel = get_class($this->channel) . "::find({$this->channel->id()})";
        $this->assertEquals($expectedLabel, $this->thumb->coverableLabel());
    }
}
