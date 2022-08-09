<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
});

/*
|--------------------------------------------------------------------------
| relations
|--------------------------------------------------------------------------
*/
it('media relation should bring media downloaded', function (): void {
    $media = Media::factory()->create();

    $download = Download::factory()->media($media)->create();

    expect($download->media)->not()->toBeNull();
    expect($download->media)->toBeInstanceOf(Media::class);
    expect($download->media->title)->toBe($media->title);
});

it('channel relation should bring channel downloaded', function (): void {
    $channel = Channel::factory()->create();

    $download = Download::factory()->channel($channel)->create();

    expect($download->channel)->not()->toBeNull();
    expect($download->channel)->toBeInstanceOf(Channel::class);
    expect($download->channel->channel_name)->toBe($channel->channel_name);
});

/*
|--------------------------------------------------------------------------
| channel downloads
|--------------------------------------------------------------------------
*/
it('should count 0 if channel record no download', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias each downloaded once.
    $channel = Channel::factory()->create();
    Media::factory()->channel($channel)->count($expectedNumberOfMedias)->create();

    $result = Download::forChannelThisDay($channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe(0);
});

it('should count downloads (1 per day) for one channel properly', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias each downloaded once.
    $channel = Channel::factory()->create();
    $medias = Media::factory()->channel($channel)->count($expectedNumberOfMedias)->create();

    // creating one download per media (count is 1)
    $medias->each(fn (Media $media) => Download::factory()->media($media)->create(['count' => 1]));

    $result = Download::forChannelThisDay($channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfMedias);
});

it('should count downloads (random per day) for one channel properly', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias
    $channel = Channel::factory()->create();
    $medias = Media::factory()->channel($channel)->count($expectedNumberOfMedias)->create();

    // creating one download per media (count is 1)
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads): void {
        $download = Download::factory()->media($media)->create();
        $expectedNumberOfDownloads += $download->count;
    });

    $result = Download::forChannelThisDay($channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfDownloads);
});

it('should count downloads only for specific channel', function (): void {
    // create some medias/channel and record some downloads for them.
    $otherMedias = Media::factory()->for(
        Channel::factory()
    )
        ->count(2)
        ->create()
    ;
    $otherMedias->each(fn (Media $media) => Download::factory()->media($media)->create(['count' => 1]));

    // creating one channel and some medias
    $channel = Channel::factory()->create();
    $medias = Media::factory()->channel($channel)->count(3)->create();

    // creating one download per media
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads): void {
        $download = Download::factory()->media($media)->create();
        $expectedNumberOfDownloads += $download->count;
    });

    $result = Download::forChannelThisDay($channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfDownloads);
});

it('should count downloads only for specific media', function (): void {
    // creating one channel and some medias
    $channel = Channel::factory()->create();
    $medias = Media::factory()->channel($channel)->count(3)->create();

    // selecting one media
    $selectedMedia = $medias->first();

    // creating some downloads for all medias
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads, $selectedMedia): void {
        $download = Download::factory()->media($media)->create();
        if ($media->is($selectedMedia)) {
            $expectedNumberOfDownloads = $download->count;
        }
    });

    $result = Download::forMediaThisDay($selectedMedia, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfDownloads);
});
