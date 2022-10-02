<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\CreateVignetteFromThumbJob;
use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\Vignette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as FacadesImage;
use Intervention\Image\Image as InterventionImage;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CreateVignetteFromThumbJobTest extends TestCase
{
    use RefreshDatabase;

    protected Thumb $thumb;
    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->thumb = $this->createCoverFor($this->channel);
    }

    /** @test */
    public function channel_cleaning_is_working_fine(): void
    {
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
