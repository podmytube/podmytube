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

    /** @var Object $someClass */
    protected $object;

    public function setUp(): void
    {
        parent::setup();
        $this->object = new class extends Model {
            use HasCover;
            public $id = 1;
        };
    }

    /** @test */
    public function playlist_cover_should_be_null()
    {
        $playlist = factory(Playlist::class)->create();
        $this->assertNull($playlist->cover);
    }

    /** @test */
    public function playlist_cover_should_be_ok()
    {
        $playlist = factory(Playlist::class)->create();
        $thumb = factory(Thumb::class)->create(
            [
                'coverable_type' => get_class($playlist),
                'coverable_id' => $playlist->id,
            ]
        );
        $this->assertNotNull($playlist->cover);
        $this->assertInstanceOf(Thumb::class, $playlist->cover);
    }

    /** @test */
    public function channel_cover_should_be_null()
    {
        $channel = factory(Channel::class)->create();
        $this->assertNull($channel->cover);
    }

    /** @test */
    public function channel_cover_should_be_ok()
    {
        $channel = factory(Channel::class)->create();
        $thumb = factory(Thumb::class)->create(
            [
                'coverable_type' => get_class($channel),
                'coverable_id' => $channel->channelId(),
            ]
        );
        $this->assertNotNull($channel->cover);
        $this->assertInstanceOf(Thumb::class, $channel->cover);
    }

    /** @test */
    public function set_cover_is_fine()
    {
        /** faking uploaded file */
        $file = UploadedFile::fake()->image('photo1.jpg');

        /** setting cover */
        $channel = factory(Channel::class)->create();
        $result = $channel->setCover($file);
        $this->assertInstanceOf(Thumb::class, $result);
        $this->assertNotNull($channel->cover);
        $this->assertInstanceOf(Thumb::class, $channel->cover);
    }

    /**
     * ===================================================================
     * Helpers
     * ===================================================================
     */
}
