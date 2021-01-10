<?php

namespace Tests\Unit;

use App\Channel;
use App\Thumb;
use App\Modules\Vignette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function testingDefaultUrl()
    {
        $expectedUrl =
            env('THUMBS_URL') . '/' . Vignette::DEFAULT_VIGNETTE_FILE;
        $this->assertEquals($expectedUrl, Vignette::defaultUrl());
    }

    public function testingFileName()
    {
        $pathParts = pathinfo($this->thumb->fileName());
        $fileName = $pathParts['filename'];
        $fileExtension = $pathParts['extension'];
        $expectedFileName = $fileName . Vignette::VIGNETTE_SUFFIX . '.' . $fileExtension;

        $this->assertEquals($expectedFileName, $this->vignette->fileName());
    }

    public function testingVignetteRelativePath()
    {
        $expectedPath = $this->channel->channel_id . '/' . $this->vignette->fileName();

        $this->assertEquals($expectedPath, $this->vignette->relativePath());
    }

    public function testLocalPath()
    {
        $expectedFilePath = Storage::disk(Thumb::LOCAL_STORAGE_DISK)
            ->path($this->vignette->relativePath());

        $this->assertEquals(
            $expectedFilePath,
            $this->vignette->localFilePath()
        );
    }

    public function testRemotePath()
    {
        $expectedFilePath = config('app.thumbs_path') . $this->vignette->relativePath();
        $this->assertEquals(
            $expectedFilePath,
            $this->vignette->remoteFilePath()
        );
    }
}
