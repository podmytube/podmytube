<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\DownloadMediaTagException;
use App\Exceptions\MediaAlreadyGrabbedException;
use App\Exceptions\MediaIsTooOldException;
use App\Factories\ShouldMediaBeingDownloadedFactory;
use App\Models\Channel;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ShouldMediaBeingDownloadedFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected Media $nonTaggedMedia;

    protected Media $taggedMedia;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->taggedMedia = Media::factory()->create(
            [
                'media_id' => self::BEACH_VOLLEY_VIDEO_1,
                'published_at' => now()->subDays(15),
            ]
        );
        $this->nonTaggedMedia = Media::factory()->create(
            [
                'media_id' => self::BEACH_VOLLEY_VIDEO_2,
                'channel_id' => $this->taggedMedia->channel_id,
                'published_at' => now()->subDays(15),
            ]
        );
    }

    /** @test */
    public function old_video_check_is_ok(): void
    {
        // no filters => accepted
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel has no date filter, media should be accepted'
        );

        $this->taggedMedia->channel->update(['reject_video_too_old' => now()->subDay()]);

        $this->expectException(MediaIsTooOldException::class);
        ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check();
    }

    /**
     * Channel has no filter every media should be accepted.
     *
     * @test
     */
    public function no_filtering_tag_at_is_ok(): void
    {
        // channel with no filter accept everything - TAGGED media
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel is filtering nothing, media with tags ["dev", "podmytube"] should be accepted.'
        );

        // channel with no filter accept everything - UNTAGGED media
        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check(),
            'Channel is filtering nothing, media with tags ["dev", "podmytube"] should be accepted.'
        );
    }

    /**
     * Only media tagged with podmytube should be accepted.
     *
     * @test
     */
    public function filtering_on_podmytube_tag_is_ok(): void
    {
        // adding accepted tag "podmytube" to channel
        $this->taggedMedia->channel->update(['accept_video_by_tag' => 'podmytube']);
        $this->nonTaggedMedia->refresh();

        $this->assertTrue(
            ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check(),
            'Channel is accepting only videos with "podmytube" tag, properly tagged media should be accepted.'
        );

        $this->expectException(DownloadMediaTagException::class);
        ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check();
    }

    /** @test */
    public function non_tagged_media_is_rejected(): void
    {
        // adding accepted tag "rejected" to channel
        $this->nonTaggedMedia->channel->update(['accept_video_by_tag' => 'rejecting']);
        $this->expectException(DownloadMediaTagException::class);
        ShouldMediaBeingDownloadedFactory::create($this->nonTaggedMedia)->check();
    }

    /** @test */
    public function tagged_media_with_wrong_tag_is_rejected(): void
    {
        $this->taggedMedia->channel->update(['accept_video_by_tag' => 'rejecting']);
        $this->expectException(DownloadMediaTagException::class);
        ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check();
    }

    /** @test */
    public function media_already_grabbed(): void
    {
        $this->taggedMedia->update(['grabbed_at' => now()->subDay()]);
        $this->expectException(MediaAlreadyGrabbedException::class);
        ShouldMediaBeingDownloadedFactory::create($this->taggedMedia)->check();
    }
}
