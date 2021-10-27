<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\VolumeOnDiskFactory;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VolumeOnDiskFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function raw_volume_on_disk_is_fine(): void
    {
        // non grabbed files are 0
        factory(Media::class, 2)->create();
        $this->assertEquals(0, VolumeOnDiskFactory::init()->raw());

        // with one only
        $media = factory(Media::class)->create(['grabbed_at' => now()]);
        $expectedVolumeOnDisk = $media->length;
        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->raw());

        // with some more
        $medias = factory(Media::class, 10)->create(['grabbed_at' => now()]);
        $expectedVolumeOnDisk = $medias->reduce(function ($carry, Media $media) {
            return $carry + $media->length;
        }, $expectedVolumeOnDisk);

        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->raw());
    }

    /** @test */
    public function formatted_volume_on_disk_is_fine(): void
    {
        // non grabbed files are 0
        factory(Media::class, 2)->create();
        $this->assertEquals(0, VolumeOnDiskFactory::init()->formatted());

        // with one only
        $media = factory(Media::class)->create(['grabbed_at' => now()]);
        $expectedVolumeOnDisk = formatBytes($media->length);
        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->formatted());

        // with some more
        $medias = factory(Media::class, 10)->create(['grabbed_at' => now()]);
        $expectedVolumeOnDisk = formatBytes($medias->reduce(function ($carry, Media $media) {
            return $carry + $media->length;
        }, $expectedVolumeOnDisk));

        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->formatted());
    }
}
