<?php

namespace Tests\Unit;

use App\Channel;
use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Thumb;
use App\Modules\Vignette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Image;
use Tests\TestCase;

class VignetteModuleTest extends TestCase
{
    /** used to remove every created data in database */
    use RefreshDatabase;

    /** @var \App\Thumb $thumb */
    protected $thumb;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Modules\Vignette $vignette */
    protected $vignette;

    public function setUp():void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        $this->thumb = factory(Thumb::class)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
        $this->vignette = Vignette::fromThumb($this->thumb);
    }

    public function tearDown():void
    {
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)->deleteDirectory($this->channel->channel_id);
        Storage::disk(Vignette::LOCAL_STORAGE_DISK)->deleteDirectory($this->channel->channel_id);
        parent::tearDown();
    }

    public function testingDefaultUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . Vignette::DEFAULT_VIGNETTE_FILE;
        $this->assertEquals($expectedUrl, Vignette::defaultUrl());
    }

    public function testingFileName()
    {
        $pathParts = pathinfo($this->thumb->fileName());
        $expectedFileName = $pathParts['filename'] . Vignette::VIGNETTE_SUFFIX . '.' . $pathParts['extension'];

        $this->assertEquals($expectedFileName, $this->vignette->fileName());
    }

    public function testingVignetteRelativePath()
    {
        $expectedPath = $this->channel->channel_id . '/' . $this->vignette->fileName();
        $this->assertEquals($expectedPath, $this->vignette->relativePath());
    }

    public function testLocalPath()
    {
        $expectedFilePath = Storage::disk(Vignette::LOCAL_STORAGE_DISK)
            ->path($this->vignette->relativePath());
        $this->assertEquals($expectedFilePath, $this->vignette->localFilePath());
    }

    public function testRemotePath()
    {
        $expectedFilePath = config('app.thumbs_path') . $this->vignette->relativePath();
        $this->assertEquals($expectedFilePath, $this->vignette->remoteFilePath());
    }

    public function testGetDataFromValidThumbReturnOneImage()
    {
        $vignetteData = $this->vignette->makeIt()->getData();
        $this->assertInstanceOf(\Intervention\Image\Image::class, Image::make($vignetteData));
    }

    public function testGetDataFromInvalidThumbThrowException()
    {
        $thumb = factory(Thumb::class)->create();
        unlink($thumb->localFilePath());

        $this->expectException(VignetteCreationFromMissingThumbException::class);
        Vignette::fromThumb($thumb)->makeIt();
    }

    public function testSaveLocally()
    {
        $this->assertFileDoesNotExist($this->vignette->localFilePath());
        $this->vignette->makeIt()->saveLocally();
        $this->assertFileExists($this->vignette->localFilePath());
    }

    public function testUrlShouldBeGood()
    {
        $this->assertEquals(
            Storage::disk(Vignette::LOCAL_STORAGE_DISK)->url($this->vignette->relativePath()),
            $this->vignette->url()
        );
    }
}
