<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use App\Traits\HasCover;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HasCoverTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setup();
        $this->playlist = Playlist::factory()->create();
        $this->channel = Channel::factory()->create();
    }

    /** @test */
    public function morphed_name_is_valid(): void
    {
        $this->assertEquals('morphedPlaylist', $this->playlist->morphedName());
        $this->assertEquals('morphedChannel', $this->channel->morphedName());
    }

    /** @test */
    public function playlist_cover_should_be_null(): void
    {
        $this->assertNull($this->playlist->cover);
        $this->assertFalse($this->playlist->hasCover());
    }

    /** @test */
    public function playlist_cover_should_be_ok(): void
    {
        Thumb::factory()->create(
            [
                'coverable_type' => $this->playlist->morphedName(),
                'coverable_id' => $this->playlist->id,
            ]
        );
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }

    /** @test */
    public function channel_cover_should_be_null(): void
    {
        $this->assertNull($this->channel->cover);
        $this->assertFalse($this->channel->hasCover());
    }

    /** @test */
    public function channel_cover_relationship_should_be_ok(): void
    {
        Thumb::factory()->create(
            [
                'coverable_type' => $this->channel->morphedName(),
                'coverable_id' => $this->channel->channelId(),
            ]
        );
        $this->assertNotNull($this->channel->cover);
        $this->assertTrue($this->channel->hasCover());
        $this->assertInstanceOf(Thumb::class, $this->channel->cover);
    }

    /** @test */
    public function set_channel_cover_is_fine(): void
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
    public function set_playlist_cover_is_fine(): void
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
    public function set_cover_from_thumb_is_fine(): void
    {
        $thumb = Thumb::factory()->create();
        $this->playlist->setCoverFromThumb($thumb);
        $this->assertNotNull($this->playlist->cover);
        $this->assertInstanceOf(Thumb::class, $this->playlist->cover);
    }

    /** @test */
    public function cover_folder_path_should_be_good(): void
    {
        // for channel
        $this->assertEquals(
            config('app.thumbs_path') . '/' . $this->channel->relativeFolderPath(),
            $this->channel->coverFolderPath()
        );

        // for playlist
        $this->assertEquals(
            config('app.thumbs_path') . '/' . $this->playlist->relativeFolderPath(),
            $this->playlist->coverFolderPath()
        );
    }

   /** @test */
   public function cover_url_should_fail_when_not_coverable(): void
   {
       $this->expectException(InvalidArgumentException::class);
       $hasCover = new class() extends Model {
           use HasCover;
       };

       $hasCover->cover_url;
   }

   /** @test */
   public function channel_vignette_url_should_return_default_url(): void
   {
       $this->assertEquals(defaultCoverUrl(), $this->channel->cover_url);
   }

   /** @test */
   public function channel_cover_url_should_return_true_cover_url(): void
   {
       $thumb = Thumb::factory()->create();
       $this->channel->attachCover($thumb);
       $coverFilepath = $this->channel->channelId() . '/' . $thumb->file_name;

       $expectedCoverUrl = Storage::disk('thumbs')->url($coverFilepath);
       $this->assertEquals($expectedCoverUrl, $this->channel->cover_url);
   }

   /** @test */
   public function playlist_cover_url_should_return_default_url(): void
   {
       $this->assertEquals(defaultCoverUrl(), $this->playlist->cover_url);
   }

   /** @test */
   public function playlist_cover_url_should_return_true_url(): void
   {
       $thumb = Thumb::factory()->create();
       $this->playlist->attachCover($thumb);

       $coverFilepath = $this->playlist->channelId() . '/' . $thumb->file_name;
       $expectedCoverUrl = Storage::disk('thumbs')->url($coverFilepath);
       $this->assertEquals($expectedCoverUrl, $this->playlist->cover_url);
   }
}
