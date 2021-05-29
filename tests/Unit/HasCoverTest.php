<?php

namespace Tests\Unit;

use App\Channel;
use App\Playlist;
use App\Thumb;
use App\Traits\HasCover;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HasCoverTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setup();
        $this->playlist = factory(Playlist::class)->create();
        $this->channel = factory(Channel::class)->create();
        $this->object = new class extends Model {
            use HasCover;
            public $id = 1;
        };
    }

    /** @test */
    public function playlist_cover_should_be_null()
    {
        $this->assertNull($this->playlist->cover);
    }

    /** @test */
    public function playlist_cover_should_be_ok()
    {
        factory(Thumb::class)->create(
            [
                'coverable_type' => get_class($this->playlist),
                'coverable_id' => $this->playlist->id,
            ]
        );
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }

    /** @test */
    public function channel_cover_should_be_null()
    {
        $this->assertNull($this->channel->cover);
        $this->assertFalse($this->channel->hasCover());
    }

    /** @test */
    public function channel_cover_should_be_ok()
    {
        factory(Thumb::class)->create(
            [
                'coverable_type' => get_class($this->channel),
                'coverable_id' => $this->channel->channelId(),
            ]
        );
        $this->assertNotNull($this->channel->cover);
        $this->assertTrue($this->channel->hasCover());
        $this->assertInstanceOf(Thumb::class, $this->channel->cover);
    }

    /** @test */
    public function set_channel_cover_is_fine()
    {
        /** faking uploaded file */
        $uploadedFile = UploadedFile::fake()->image('photo1.jpg');

        /** setting cover */
        $result = $this->channel->setCoverFromUploadedFile($uploadedFile);
        $this->assertInstanceOf(Thumb::class, $result);
        $this->assertNotNull($this->channel->cover);
        $this->assertInstanceOf(Thumb::class, $this->channel->cover);
    }

    /** @test */
    public function set_playlist_cover_is_fine()
    {
        /** faking uploaded file */
        $uploadedFile = UploadedFile::fake()->image('photo1.jpg');

        /** setting cover */
        $result = $this->playlist->setCoverFromUploadedFile($uploadedFile);
        $this->assertInstanceOf(Thumb::class, $result);
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }

    /** @test */
    public function set_cover_from_thumb_is_fine()
    {
        $thumb = factory(Thumb::class)->create();
        $this->playlist->setCoverFromThumb($thumb);
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }

    /**
     * ===================================================================
     * Helpers
     * ===================================================================
     */
}
