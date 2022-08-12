<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\VolumeOnDiskFactory;
use App\Models\Media;
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
        Media::factory()->count(2)->create();
        $this->assertEquals(0, VolumeOnDiskFactory::init()->raw());

        // with one only
        $media = Media::factory()->create();
        $expectedVolumeOnDisk = $media->length;
        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->raw());

        // with some more
        $medias = Media::factory()->count(10)->grabbedAt(now())->create();
        $expectedVolumeOnDisk = $medias->reduce(function ($carry, Media $media) {
            return $carry + $media->weight();
        }, $expectedVolumeOnDisk);

        $this->assertEquals($expectedVolumeOnDisk, VolumeOnDiskFactory::init()->raw());
    }

    /** @test */
    public function formatted_volume_on_disk_is_fine(): void
    {
        // non grabbed files are 0
        Media::factory()->count(2)->create();
        $this->assertEquals(0, VolumeOnDiskFactory::init()->formatted());

        // with one only
        $media = Media::factory()->grabbedAt(now())->create();
        $expectedVolumeOnDisk = $media->weight();
        $this->assertEquals(formatBytes($media->weight()), VolumeOnDiskFactory::init()->formatted());

        // with some more
        $medias = Media::factory()->grabbedAt(now())->count(10)->create();

        $expectedVolumeOnDisk = $medias->reduce(
            fn ($carry, Media $media) => $carry + $media->weight(),
            $expectedVolumeOnDisk
        );

        $expectedVolumeOnDiskFormatted = formatBytes($expectedVolumeOnDisk);

        $this->assertEquals($expectedVolumeOnDiskFormatted, VolumeOnDiskFactory::init()->formatted());
    }
}
