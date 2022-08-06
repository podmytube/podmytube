<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Modules\Vignette;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Image;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VignetteModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Thumb */
    protected $thumb;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Modules\Vignette */
    protected $vignette;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
        $this->thumb = $this->createCoverFor($this->channel);
        $this->vignette = Vignette::fromThumb($this->thumb);
    }

    public function tearDown(): void
    {
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)->deleteDirectory($this->channel->channel_id);
        Storage::disk(Vignette::LOCAL_STORAGE_DISK)->deleteDirectory($this->channel->channel_id);
        parent::tearDown();
    }

    public function testing_default_url(): void
    {
        $expectedUrl = env('THUMBS_URL') . '/' . Vignette::DEFAULT_VIGNETTE_FILE;
        $this->assertEquals($expectedUrl, Vignette::defaultUrl());
    }

    public function testing_file_name(): void
    {
        $pathParts = pathinfo($this->thumb->fileName());
        $expectedFileName = $pathParts['filename'] . Vignette::VIGNETTE_SUFFIX . '.' . $pathParts['extension'];

        $this->assertEquals($expectedFileName, $this->vignette->fileName());
    }

    public function testing_vignette_relative_path(): void
    {
        $expectedPath = $this->channel->channel_id . '/' . $this->vignette->fileName();
        $this->assertEquals($expectedPath, $this->vignette->relativePath());
    }

    public function test_local_path(): void
    {
        $expectedFilePath = Storage::disk(Vignette::LOCAL_STORAGE_DISK)
            ->path($this->vignette->relativePath())
        ;
        $this->assertEquals($expectedFilePath, $this->vignette->localFilePath());
    }

    public function test_remote_path(): void
    {
        $expectedFilePath = config('app.thumbs_path') . $this->vignette->relativePath();
        $this->assertEquals($expectedFilePath, $this->vignette->remoteFilePath());
    }

    public function test_get_data_from_valid_thumb_return_one_image(): void
    {
        $vignetteData = $this->vignette->makeIt()->getData();
        $this->assertInstanceOf(\Intervention\Image\Image::class, Image::make($vignetteData));
    }

    public function test_get_data_from_invalid_thumb_throw_exception(): void
    {
        unlink($this->thumb->localFilePath());

        $this->expectException(VignetteCreationFromMissingThumbException::class);
        Vignette::fromThumb($this->thumb)->makeIt();
    }

    /** @test */
    public function save_locally_should_be_good(): void
    {
        $this->assertFileDoesNotExist($this->vignette->localFilePath());
        $this->vignette->makeIt()->saveLocally();
        $this->assertFileExists($this->vignette->localFilePath());
    }

    /** @test */
    public function url_should_be_good(): void
    {
        $this->assertEquals(
            Storage::disk(Vignette::LOCAL_STORAGE_DISK)->url($this->vignette->relativePath()),
            $this->vignette->url()
        );
    }
}
