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

    protected Channel $channel;
    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->playlist = Playlist::factory()->create();
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
        $vignetteFilepath = $this->channel->channelId() . '/' .
            pathinfo($thumb->file_name, PATHINFO_FILENAME) .
            '_vig.' .
            pathinfo($thumb->file_name, PATHINFO_EXTENSION);
        $expectedVignetteUrl = Storage::disk('vignettes')->url($vignetteFilepath);
        $this->assertEquals($expectedVignetteUrl, $this->channel->vignette_url);
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
        $expectedVignetteUrl = Storage::disk('vignettes')->url($vignetteFilepath);
        $this->assertEquals($expectedVignetteUrl, $this->playlist->vignette_url);
    }
}
