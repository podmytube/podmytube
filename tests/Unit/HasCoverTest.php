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
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class HasCoverTest extends TestCase
{
    use Covers;
    use RefreshDatabase;

    public const COVER_DISK_NAME = 'thumbs';

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
        $this->playlist->attachCover(Thumb::factory()->create());
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
        $this->channel->attachCover(Thumb::factory()->create());
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
    public function cover_full_path_should_be_good(): void
    {
        // for channel
        $this->channel->attachCover(Thumb::factory()->create());
        $this->assertEquals(
            config('app.thumbs_path') . '/' . $this->channel->coverRelativePath(),
            $this->channel->coverFullPath()
        );

        // for playlist
        $this->playlist->attachCover(Thumb::factory()->create());
        $this->assertEquals(
            config('app.thumbs_path') . '/' . $this->playlist->coverRelativePath(),
            $this->playlist->coverFullPath()
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

   /** @test */
   public function cover_exists_should_work_fine(): void
   {
       // no thumb
       $this->assertFalse($this->channel->coverFileExists());

       // thumb without vignette file should return false
       $this->channel->attachCover(Thumb::factory()->create());
       $this->channel->refresh();

       // touching file
       Storage::disk(self::COVER_DISK_NAME)->put($this->coverFilePath($this->channel), '');
       $this->assertTrue($this->channel->coverFileExists());
   }
}
