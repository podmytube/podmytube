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

    /** \App\Media $nonTaggedMedia */
    protected $nonTaggedMedia;

    /** \App\Media $taggedMedia */
    protected $taggedMedia;

    public function setUp():void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->taggedMedia = factory(Media::class)->create(
            [
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
                'published_at' => now()->subDays(15),
            ]
        );
        $this->nonTaggedMedia = factory(Media::class)->create(
            [
                'media_id' => self::BEACH_VOLLEY_VIDEO_2,
                'channel_id' => $this->taggedMedia->channel_id,
                'published_at' => now()->subDays(15),
            ]
        );
    }

    /** @test */
    public function old_video_check_is_ok()
    {
        /** no filters => accepted */
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel has no date filter, media should be accepted'
        );

        $this->taggedMedia->channel->update(['reject_video_too_old' => now()->subDay()]);

        $this->expectException(MediaIsTooOldException::class);
        ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check();
    }

    /** @test */
    public function tag_checking_is_ok()
    {
        /** channel with no filter accept everything - TAGGED media */
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel is filtering nothing, media with tags ["dev", "podmytube"] should be accepted.'
        );

        /** channel with no filter accept everything - UNTAGGED media */
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check(),
            'Channel is filtering nothing, media with tags ["dev", "podmytube"] should be accepted.'
        );

        /**
         * adding accepted tag "podmytube" to channel
         */
        $this->taggedMedia->channel->update(['accept_video_by_tag' => 'podmytube', ]);
        $this->nonTaggedMedia->refresh();
        $this->assertFalse(
            ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check(),
            'Channel is accepting only videos with "podmytube" tag, non tagged media with no tag should be rejected.'
        );
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel is accepting only videos with "podmytube" tag, properly tagged media should be accepted.'
        );

        /**
         * adding accepted tag "rejected" to channel
         */
        $this->taggedMedia->channel->update(['accept_video_by_tag' => 'rejected', ]);
        $this->nonTaggedMedia->refresh();
        $this->assertFalse(
            ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check(),
            'Channel is accepting only videos with "rejected" tag, non tagged media with no tag should be rejected.'
        );
        $this->assertFalse(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel is accepting only videos with "rejected" tag, uproperly tagged media should be rejected too.'
        );
    }
}
