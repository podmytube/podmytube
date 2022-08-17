<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
    $this->channel = Channel::factory()->create();
});

/*
|--------------------------------------------------------------------------
| relations
|--------------------------------------------------------------------------
*/
it('media relation should bring media downloaded', function (): void {
    $media = $this->addMediasToChannel($this->channel);

    $download = Download::factory()->media($media)->create();

    expect($download->media)->not()->toBeNull();
    expect($download->media)->toBeInstanceOf(Media::class);
    expect($download->media->title)->toBe($media->title);
});

it('channel relation should bring channel downloaded', function (): void {
    $download = Download::factory()->channel($this->channel)->create();

    expect($download->channel)->not()->toBeNull();
    expect($download->channel)->toBeInstanceOf(Channel::class);
    expect($download->channel->channel_name)->toBe($this->channel->channel_name);
});

/*
|--------------------------------------------------------------------------
| channel downloads
|--------------------------------------------------------------------------
*/
it('should count 0 if channel record no download', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias each downloaded once.
    $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

    $result = Download::forChannelThisDay($this->channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe(0);
});

it('should count downloads (1 per day) for one channel properly', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias each downloaded once.
    $medias = $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

    // creating one download per media (count is 1)
    $medias->each(fn (Media $media) => Download::factory()->media($media)->create(['counted' => 1]));

    $result = Download::forChannelThisDay($this->channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfMedias);
});

it('should count downloads (random per day) for one channel properly', function (): void {
    $expectedNumberOfMedias = 3;

    // creating one channel and some medias
    $medias = $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

    // creating one download per media (count is 1)
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads): void {
        $download = Download::factory()->media($media)->create();
        $expectedNumberOfDownloads += $download->counted;
    });

    $result = Download::forChannelThisDay($this->channel, now());
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

    $otherMedias->each(fn (Media $media) => Download::factory()->media($media)->create(['counted' => 1]));

    // creating one channel and some medias
    $medias = $this->addMediasToChannel($this->channel, 3);

    // creating one download per media
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads): void {
        $download = Download::factory()->media($media)->create();
        $expectedNumberOfDownloads += $download->counted;
    });

    $result = Download::forChannelThisDay($this->channel, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfDownloads);
});

it('should count downloads only for specific media', function (): void {
    // creating one channel and some medias
    $medias = $this->addMediasToChannel($this->channel, 3);

    // selecting one media
    $selectedMedia = $medias->first();

    // should be 0 --- before any download
    $result = Download::forMediaThisDay($selectedMedia, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe(0);

    // creating some downloads for all medias
    $expectedNumberOfDownloads = 0;
    $medias->each(function (Media $media) use (&$expectedNumberOfDownloads, $selectedMedia): void {
        $download = Download::factory()->media($media)->create();
        if ($media->is($selectedMedia)) {
            $expectedNumberOfDownloads = $download->counted;
        }
    });

    $result = Download::forMediaThisDay($selectedMedia, now());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedNumberOfDownloads);
});

it('should count downloads during one month', function (): void {
    // basic test
    // creating one media
    $media = $this->addMediasToChannel($this->channel);

    $startOfJuly2022 = Carbon::create(2022, 7, 1);
    $endOfMonth = (clone $startOfJuly2022)->endOfDay()->endOfMonth();

    // addDownloadsForMediaDuringPeriod is creating one row per day into downloads
    // it returns sum('counted') as a result
    $expectedDownloadsCounted = addDownloadsForMediaDuringPeriod($media, $startOfJuly2022, $endOfMonth);

    $expectedDownloadRows = 31;

    // only checking I'm doing right for nox
    expect(Download::count())->toBe($expectedDownloadRows);
    expect(Download::sum('counted'))->toBe($expectedDownloadsCounted);

    // real test
    $wantedPeriod = PeriodsHelper::create(7, 2022);

    // for this channel should be good
    expect(Download::downloadsForChannelDuringPeriod(
        channel: $this->channel,
        startDate: $wantedPeriod->startDate(),
        endDate: $wantedPeriod->endDate(),
    ))->toBe($expectedDownloadsCounted);

    // for this media should be good
    expect(Download::downloadsForMediaDuringPeriod(
        media: $media,
        startDate: $wantedPeriod->startDate(),
        endDate: $wantedPeriod->endDate(),
    ))->toBe($expectedDownloadsCounted);

    // all the counted downloads
    expect(Download::downloadsDuringPeriod(startDate: $wantedPeriod->startDate(), endDate: $wantedPeriod->endDate()))
        ->toBe($expectedDownloadsCounted)
    ;
});

it('should count downloads for the right item', function (): void {
    // basic test
    $media = $this->addMediasToChannel($this->channel);

    // playing with dates
    $startOfJuly2022 = Carbon::create(2022, 7, 1);
    $startDate = clone $startOfJuly2022;
    $endOfMonth = (clone $startOfJuly2022)->endOfDay()->endOfMonth();

    // addDownloadsForMediaDuringPeriod is creating one row per day into downloads
    // it returns sum('counted') for the media as a result
    $expectedDownloadsCountedForMedia = addDownloadsForMediaDuringPeriod($media, $startDate, $endOfMonth);

    // reset startDate we will need it soon
    $startDate = clone $startOfJuly2022;

    // creating another channel with medias
    $anotherChannel = Channel::factory()->hasMedias(2)->create();
    // add download row for all anotherChannel medias (one by day)
    $otherDownloadsCounted = addDownloadsForChannelMediasDuringPeriod($anotherChannel, $startDate, $endOfMonth);

    $totalCounted = $expectedDownloadsCountedForMedia + $otherDownloadsCounted;
    // july 2022 has 31 days and we have 3 medias created
    // $channel->media and $anotherChannel with 2 medias = 3
    $expectedDownloadRows = 31 * 3;

    // only checking I'm doing right for nox
    expect(Download::count())->toBe($expectedDownloadRows);
    expect(Download::sum('counted'))->toBe($totalCounted);

    // real test
    $wantedPeriod = PeriodsHelper::create(7, 2022);

    // for this channel should be good
    expect(Download::downloadsForChannelDuringPeriod(
        channel: $this->channel,
        startDate: $wantedPeriod->startDate(),
        endDate: $wantedPeriod->endDate(),
    ))->toBe($expectedDownloadsCountedForMedia);

    // for this media should be good
    expect(Download::downloadsForMediaDuringPeriod(
        media: $media,
        startDate: $wantedPeriod->startDate(),
        endDate: $wantedPeriod->endDate(),
    ))->toBe($expectedDownloadsCountedForMedia);

    // all the counted downloads
    expect(Download::downloadsDuringPeriod(startDate: $wantedPeriod->startDate(), endDate: $wantedPeriod->endDate()))
        ->toBe($totalCounted)
    ;
});

it('wip should count all downloads day', function (): void {
    $expectedDownloads = 100;
    Download::factory()->channel($this->channel)->create(['counted' => 75]);
    Download::factory()->channel(Channel::factory()->create())->create(['counted' => 25]);

    ray(Download::all()->toArray())->showQueries();
    $result = Download::downloadsDuringPeriod(now()->startOfDay(), now()->endOfDay());
    expect($result)->not()->toBeNull();
    expect($result)->toBeInt();
    expect($result)->toBe($expectedDownloads);
});

/*
|--------------------------------------------------------------------------
| helpers & providers
|--------------------------------------------------------------------------
*/

/**
 * @return int nb of counted downloads
 */
function addDownloadsForMediaDuringPeriod(Media $media, Carbon $startDate, Carbon $endDate): int
{
    $countedDownloads = 0;
    while ($startDate->lessThan($endDate)) {
        $download = Download::factory()->media($media)->logDate($startDate)->create();
        $countedDownloads += $download->counted;
        $startDate->addDay();
    }

    return $countedDownloads;
}

/**
 * @return int nb of counted downloads
 */
function addDownloadsForChannelMediasDuringPeriod(Channel $channel, Carbon $startDate, Carbon $endDate): int
{
    $totalDownloads = 0;
    while ($startDate->lessThan($endDate)) {
        $totalDownloads = $channel->medias->reduce(function ($carry, Media $media) use ($startDate) {
            $download = Download::factory()
                ->media($media)
                ->logDate($startDate)
                ->create()
            ;

            return $carry + $download->counted;
        }, $totalDownloads);
        $startDate->addDay();
    }

    return $totalDownloads;
}
