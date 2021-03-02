<?php

namespace Tests\Feature;

use App\Channel;
use App\Exceptions\MediaIsTooOldException;
use App\Factories\ShouldMediaBeingDownloadedFactory;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ShouldMediaBeingDownloadedFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** \App\Channel $channel */
    protected $channel;

    /** \App\Media $media */
    protected $media;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->channel = factory(Channel::class)->create();
        $this->media = factory(Media::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
                'published_at' => now()->subDays(15),
            ]
        );
    }

    /** @test */
    public function old_video_check_is_ok()
    {
        /** no filters => accepted  */
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->media)->check(),
            'Channel has no date filter, media should be accepted'
        );

        $this->channel->update(['reject_video_too_old' => now()->subDay()]);
        $this->media->refresh();

        $this->expectException(MediaIsTooOldException::class);
        ShouldMediaBeingDownloadedFactory::create($this->media)->check();
    }
}
