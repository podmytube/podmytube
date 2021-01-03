<?php

namespace Tests\Unit;

use App\Thumb;
use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThumbModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var bool true, database is ready to run tests upon */
    protected static $dbIsWarm = false;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        factory(Thumb::class)->create(['channel_id' => $this->channel->channelId(), ]);
    }

    public function testingDefaultUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . Thumb::DEFAULT_THUMB_FILE;
        $this->assertEquals($expectedUrl, Thumb::defaultUrl());
    }

    public function testingThumbExists()
    {
        $this->assertTrue($this->channel->thumb->exists());
    }

    public function testingFileName()
    {
        $this->assertEquals($this->channel->thumb->file_name, $this->channel->thumb->fileName());
    }

    public function testingRelativePath()
    {
        $expectedResult =
            $this->channel->channelId() . '/' . $this->channel->thumb->fileName();
        $this->assertEquals($expectedResult, $this->channel->thumb->relativePath());
    }

    public function testingPodcastUrl()
    {
        $expectedUrl = env('THUMBS_URL') . '/' . $this->channel->thumb->relativePath();
        $this->assertEquals($expectedUrl, $this->channel->thumb->podcastUrl());
    }

    public function testingThumbDoesNotExist()
    {
        $thumb = new Thumb();
        $this->assertFalse($thumb->exists());
    }

    public function testRemotePath()
    {
        $expectedFilePath = config('app.thumbs_path') . $this->channel->channel_id . '/' . $this->channel->thumb->file_name;
        $this->assertEquals(
            $expectedFilePath,
            $this->channel->thumb->remoteFilePath()
        );
    }
}
