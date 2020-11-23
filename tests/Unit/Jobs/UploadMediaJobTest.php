<?php

namespace Tests\Unit\Jobs;

use App\Channel;
use App\Jobs\UploadMediaJob;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class UploadMediaJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $media */
    protected $media;

    /** @var string */
    protected $mp3File;

    public function testUploadMediaHandlingWorksFine()
    {
        $this->prepare();

        /** local file should exist */
        $this->assertTrue(Storage::disk('uploadedMedias')->exists($this->mp3File));

        $uploadMediaJob = new UploadMediaJob($this->media);
        $uploadMediaJob->handle();

        $this->assertTrue(Storage::disk(Media::REMOTE_DISK)->exists($this->media->relativePath()));
    }

    protected function prepare()
    {
        /** fake channel */
        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);

        /** create fake media */
        $this->media = factory(Media::class)->create(['channel_id' => $this->channel->channel_id]);

        $this->mp3File = $this->media->media_id . '.mp3';
        /** create file to be uploaded */
        Storage::disk('uploadedMedias')->put(
            $this->mp3File,
            file_get_contents(__DIR__ . '/../../fixtures/Audio/qfx6yf8pux4.mp3')
        );
    }
}
