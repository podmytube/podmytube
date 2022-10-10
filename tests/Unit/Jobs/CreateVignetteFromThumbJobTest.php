<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\CreateVignetteFromThumbJob;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use App\Modules\Vignette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as FacadesImage;
use Intervention\Image\Image as InterventionImage;
use Tests\TestCase;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class CreateVignetteFromThumbJobTest extends TestCase
{
    use Covers;
    use RefreshDatabase;

    protected Thumb $thumb;
    protected Channel $channel;
    protected Playlist $playlist;

    /** @test */
    public function creating_vignette_for_channel_is_working_fine(): void
    {
        $this->channel = Channel::factory()->create();
        $this->thumb = $this->createCoverFor($this->channel);

        $job = new CreateVignetteFromThumbJob($this->thumb);
        $job->handle();

        $vignette = $job->vignette();
        $this->assertNotNull($vignette);
        $this->assertInstanceOf(Vignette::class, $vignette);

        $expectedRelativePath = $vignette->relativePath();
        Storage::disk(Vignette::LOCAL_STORAGE_DISK)->assertExists($expectedRelativePath);

        // check file is an image
        $image = FacadesImage::make(Storage::disk(Vignette::LOCAL_STORAGE_DISK)->get($expectedRelativePath));

        $this->assertNotNull($image);
        $this->assertInstanceOf(InterventionImage::class, $image);

        // check file has rights dimensions
        $this->assertEquals(config('app.vignette_width'), $image->width());
        $this->assertEquals(config('app.vignette_height'), $image->height());
    }

    /** @test */
    public function creating_vignette_for_playlist_is_working_fine(): void
    {
        $this->channel = Channel::factory()->create();
        $this->playlist = Playlist::factory()->channel($this->channel)->create();
        $this->thumb = $this->createCoverFor($this->playlist);

        $job = new CreateVignetteFromThumbJob($this->thumb);
        $job->handle();

        $vignette = $job->vignette();
        $this->assertNotNull($vignette);
        $this->assertInstanceOf(Vignette::class, $vignette);

        $expectedRelativePath = $vignette->relativePath();
        Storage::disk(Vignette::LOCAL_STORAGE_DISK)->assertExists($expectedRelativePath);

        // check file is an image
        $image = FacadesImage::make(Storage::disk(Vignette::LOCAL_STORAGE_DISK)->get($expectedRelativePath));

        $this->assertNotNull($image);
        $this->assertInstanceOf(InterventionImage::class, $image);

        // check file has rights dimensions
        $this->assertEquals(config('app.vignette_width'), $image->width());
        $this->assertEquals(config('app.vignette_height'), $image->height());
    }
}
