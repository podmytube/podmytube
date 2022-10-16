<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use App\Traits\HasVignette;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class HasVignetteTest extends TestCase
{
    use Covers;
    use RefreshDatabase;

    public const VIGNETTE_DISK_NAME = 'vignettes';

    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->playlist = Playlist::factory()->create();
    }

    public function tearDown(): void
    {
        if ($this->channel->hasVignette()) {
            Storage::disk(self::VIGNETTE_DISK_NAME)->delete($this->channel->vignetteRelativePath());
        }
        if ($this->playlist->hasVignette()) {
            Storage::disk(self::VIGNETTE_DISK_NAME)->delete($this->playlist->vignetteRelativePath());
        }
        parent::tearDown();
    }

    /** @test */
    public function has_vignette_should_be_fine(): void
    {
        // no thumb/cover => no vignette
        $this->assertFalse($this->channel->hasVignette());

        // a thumb with no file => no vignette
        $thumb = Thumb::factory()->create();
        $this->channel->attachCover($thumb);
        $this->channel->refresh();
        $this->assertFalse($this->channel->hasVignette());

        // touching file
        Storage::disk(self::VIGNETTE_DISK_NAME)->put($this->vignetteFilePath($this->channel), '');
        $this->assertTrue($this->channel->hasVignette());
    }

    /** @test */
    public function vignette_url_should_fail_when_not_coverable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $hasVignette = new class() extends Model {
            use HasVignette;
        };

        $hasVignette->vignette_url;
    }

    /** @test */
    public function channel_vignette_url_should_return_default_vignette_url(): void
    {
        $this->assertEquals(defaultVignetteUrl(), $this->channel->vignette_url);
    }

    /** @test */
    public function channel_vignette_url_should_return_true_vignette_url(): void
    {
        $thumb = Thumb::factory()->create();
        $this->channel->attachCover($thumb);
        $this->channel->refresh();

        $expectedVignetteUrl = Storage::disk(self::VIGNETTE_DISK_NAME)->url($this->vignetteFilePath($this->channel));
        $this->assertEquals($expectedVignetteUrl, $this->channel->vignette_url);
    }

    /** @test */
    public function with_cover_vignette_relative_path_should_be_fine(): void
    {
        // thumb without vignette file should return false
        $thumb = Thumb::factory()->create();
        $this->channel->attachCover($thumb);

        $this->assertEquals($this->vignetteFilePath($this->channel), $this->channel->vignetteRelativePath());
    }

    /** @test */
    public function vignette_exists_should_work_fine(): void
    {
        // no thumb no vignette
        $this->assertFalse($this->channel->vignetteFileExists());

        // thumb without vignette file should return false
        $thumb = Thumb::factory()->create();
        $this->channel->attachCover($thumb);
        $this->channel->refresh();

        // touching file
        Storage::disk(self::VIGNETTE_DISK_NAME)->put($this->vignetteFilePath($this->channel), '');
        $this->assertTrue($this->channel->vignetteFileExists());
    }

    /** @test */
    public function playlist_vignette_url_should_return_default_vignette_url(): void
    {
        $this->assertEquals(defaultVignetteUrl(), $this->playlist->vignette_url);
    }

    /** @test */
    public function playlist_vignette_url_should_return_true_vignette_url(): void
    {
        $thumb = Thumb::factory()->create();
        $this->playlist->attachCover($thumb);

        $vignetteFilepath = $this->playlist->channelId() . '/' .
            pathinfo($thumb->file_name, PATHINFO_FILENAME) .
            '_vig.' .
            pathinfo($thumb->file_name, PATHINFO_EXTENSION);
        $expectedVignetteUrl = Storage::disk(self::VIGNETTE_DISK_NAME)->url($vignetteFilepath);
        $this->assertEquals($expectedVignetteUrl, $this->playlist->vignette_url);
    }
}
