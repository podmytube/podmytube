<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Downloads;

/**
 * @internal
 *
 * @coversNothing
 */
class DownloadsModelTest extends TestCase
{
    use Downloads;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function media_relation_should_bring_media_downloaded(): void
    {
        $media = $this->addMediasToChannel($this->channel);

        $download = Download::factory()->media($media)->create();

        $this->assertNotNull($download->media);
        $this->assertInstanceOf(Media::class, $download->media);
        $this->assertEquals($download->media->title, $media->title);
    }

    /** @test */
    public function channel_relation_should_bring_channel_downloaded(): void
    {
        $download = Download::factory()->channel($this->channel)->create();

        $this->assertNotNull($download->channel);
        $this->assertInstanceOf(Channel::class, $download->channel);
        $this->assertEquals($download->channel->channel_name, $this->channel->channel_name);
    }

    /** @test */
    public function should_count_0_when_no_downloads_recorded(): void
    {
        $expectedNumberOfMedias = 3;

        // creating one channel and some medias each downloaded once.
        $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

        // creating another channel with downloads (for comparison)
        $this->createAnotherChannelWithDownloads(now(), now());

        $result = Download::forChannelThisDay($this->channel, now());

        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    /** @test */
    public function it_should_count_downloads_properly(): void
    {
        $expectedNumberOfMedias = 3;

        // creating one channel and some medias each downloaded once.
        $medias = $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

        // creating one download per media (count is 1)
        $medias->each(fn (Media $media) => Download::factory()->media($media)->create(['counted' => 1]));

        // creating another channel with downloads (for comparison)
        $this->createAnotherChannelWithDownloads(now(), now());

        $result = Download::forChannelThisDay($this->channel, now());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedNumberOfMedias);
    }

    /** @test */
    public function it_count_downloads_random_per_day_for_one_channel_properly(): void
    {
        $expectedNumberOfMedias = 3;

        // creating one channel and some medias
        $medias = $this->addMediasToChannel($this->channel, $expectedNumberOfMedias);

        // creating one download per media (count is 1)
        $expectedNumberOfDownloads = 0;
        $medias->each(function (Media $media) use (&$expectedNumberOfDownloads): void {
            $download = Download::factory()->media($media)->create();
            $expectedNumberOfDownloads += $download->counted;
        });

        // creating another channel with downloads (for comparison)
        $this->createAnotherChannelWithDownloads(now(), now());

        $result = Download::forChannelThisDay($this->channel, now());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedNumberOfDownloads);
    }

    /** @test */
    public function it_should_count_downloads_only_for_specific_channel(): void
    {
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

        // creating another channel with downloads (for comparison)
        $this->createAnotherChannelWithDownloads(now(), now());

        $result = Download::forChannelThisDay($this->channel, now());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedNumberOfDownloads);
    }

    /** @test */
    public function should_count_downloads_only_for_specific_media(): void
    {
        // creating one channel and some medias
        $medias = $this->addMediasToChannel($this->channel, 3);

        // selecting one media
        /** @var Media $selectedMedia */
        $selectedMedia = $medias->first();

        // should be 0 --- before any download
        $result = Download::forMediaThisDay($selectedMedia, now());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, 0);

        // creating some downloads for all medias
        $expectedNumberOfDownloads = 0;
        $medias->each(function (Media $media) use (&$expectedNumberOfDownloads, $selectedMedia): void {
            $download = Download::factory()->media($media)->create();
            if ($media->is($selectedMedia)) {
                $expectedNumberOfDownloads = $download->counted;
            }
        });

        // creating another channel with downloads (for comparison)
        $this->createAnotherChannelWithDownloads(now(), now());

        $result = Download::forMediaThisDay($selectedMedia, now());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedNumberOfDownloads);
    }

    /** @test */
    public function it_should_count_downloads_during_one_month(): void
    {
        // basic test
        // creating one media
        $media = $this->addMediasToChannel($this->channel);

        $startOfJuly2022 = Carbon::create(2022, 7, 1);
        $endOfMonth = (clone $startOfJuly2022)->endOfDay()->endOfMonth();

        // addDownloadsForMediaDuringPeriod is creating one row per day into downloads
        // it returns sum('counted') as a result
        $expectedDownloadsCounted = $this->addDownloadsForMediaDuringPeriod($media, $startOfJuly2022, $endOfMonth);

        $expectedDownloadRows = 31;

        // only checking I'm doing right for nox
        $this->assertEquals(Download::count(), $expectedDownloadRows);
        $this->assertEquals(Download::sum('counted'), $expectedDownloadsCounted);

        // real test
        $wantedPeriod = PeriodsHelper::create(7, 2022);

        // for this channel should be good
        $this->assertEquals(Download::sumOfDownloadsForChannelDuringPeriod(
            channel: $this->channel,
            startDate: $wantedPeriod->startDate(),
            endDate: $wantedPeriod->endDate(),
        ), $expectedDownloadsCounted);

        // for this media should be good
        $this->assertEquals(Download::sumOfDownloadsForMediaDuringPeriod(
            media: $media,
            startDate: $wantedPeriod->startDate(),
            endDate: $wantedPeriod->endDate(),
        ), $expectedDownloadsCounted);

        // all the counted downloads
        $this->assertEquals(
            Download::downloadsDuringPeriod(startDate: $wantedPeriod->startDate(), endDate: $wantedPeriod->endDate()),
            $expectedDownloadsCounted
        );
    }

    /** @test */
    public function it_should_count_downloads_for_the_right_item(): void
    {
        // basic test
        $media = $this->addMediasToChannel($this->channel);

        // playing with dates
        $startOfJuly2022 = Carbon::create(2022, 7, 1);
        $startDate = clone $startOfJuly2022;
        $endOfMonth = (clone $startOfJuly2022)->endOfDay()->endOfMonth();

        // addDownloadsForMediaDuringPeriod is creating one row per day into downloads
        // it returns sum('counted') for the media as a result
        $expectedDownloadsCountedForMedia = $this->addDownloadsForMediaDuringPeriod($media, $startDate, $endOfMonth);

        // reset startDate we will need it soon
        $startDate = clone $startOfJuly2022;

        // creating another channel with medias
        $anotherChannel = Channel::factory()->hasMedias(2)->create();
        // add download row for all anotherChannel medias (one by day)
        $otherDownloadsCounted = $this->addDownloadsForChannelMediasDuringPeriod($anotherChannel, $startDate, $endOfMonth);

        $totalCounted = $expectedDownloadsCountedForMedia + $otherDownloadsCounted;
        // july 2022 has 31 days and we have 3 medias created
        // $channel->media and $anotherChannel with 2 medias = 3
        $expectedDownloadRows = 31 * 3;

        // only checking I'm doing right for now
        $this->assertEquals(Download::count(), $expectedDownloadRows);
        $this->assertEquals(Download::sum('counted'), $totalCounted);

        // real test
        $wantedPeriod = PeriodsHelper::create(7, 2022);

        // for this channel should be good
        $this->assertEquals(Download::sumOfDownloadsForChannelDuringPeriod(
            channel: $this->channel,
            startDate: $wantedPeriod->startDate(),
            endDate: $wantedPeriod->endDate(),
        ), $expectedDownloadsCountedForMedia);

        // for this media should be good
        $this->assertEquals(Download::sumOfDownloadsForMediaDuringPeriod(
            media: $media,
            startDate: $wantedPeriod->startDate(),
            endDate: $wantedPeriod->endDate(),
        ), $expectedDownloadsCountedForMedia);

        // all the counted downloads
        $this->assertEquals(
            Download::downloadsDuringPeriod(startDate: $wantedPeriod->startDate(), endDate: $wantedPeriod->endDate()),
            $totalCounted
        );
    }

    /** @test */
    public function should_count_all_downloads_day(): void
    {
        $expectedDownloads = 100;
        Download::factory()->channel($this->channel)->create(['counted' => 75]);
        Download::factory()->channel(Channel::factory()->create())->create(['counted' => 25]);

        $result = Download::downloadsDuringPeriod(now()->startOfDay(), now()->endOfDay());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedDownloads);
    }

    /** @test */
    public function it_should_count_all_downloads_day(): void
    {
        $expectedDownloads = 100;
        Download::factory()->channel($this->channel)->create(['counted' => 75]);
        Download::factory()->channel(Channel::factory()->create())->create(['counted' => 25]);

        $result = Download::downloadsDuringPeriod(now()->startOfDay(), now()->endOfDay());
        $this->assertNotNull($result);
        $this->assertIsInt($result);
        $this->assertEquals($result, $expectedDownloads);
    }

    /** @test */
    public function it_should_get_downloads_day_by_day_for_one_channel(): void
    {
        $this->addMediasToChannel($this->channel);

        // I want 4 download rows
        $expectedRows = 4;
        $startDate = Carbon::create(2022, 8, 15);
        $endDate = (clone $startDate)->addDays($expectedRows);

        $this->addDownloadsForChannelMediasDuringPeriod($this->channel, $startDate, $endDate);

        $results = Download::downloadsByInterval(
            channel: $this->channel,
            startDate: Carbon::create('first day of august 2022', 'Europe/Paris'),
            endDate: Carbon::create('last day of august 2022', 'Europe/Paris')
        );
        $this->assertNotNull($results);
        $this->AssertInstanceOf(Collection::class, $results);
        $this->assertCount($expectedRows, $results);
    }

    /** @test */
    public function it_should_get_downloads_by_period_group_by_channel(): void
    {
        $channels = Channel::factory()->count(3)->create();

        $channels->each(fn (Channel $channel) => Media::factory()->channel($channel)->create());

        $august2022 = Carbon::create(2022, 8, 01);
        $startDate = clone $august2022;
        $endDate = (clone $startDate)->endOfMonth();

        $hugeChannel = $channels->get(0);
        $bigChannel = $channels->get(1);
        $smallChannel = $channels->get(2);

        $hugeMedia = $hugeChannel->medias->first();
        $bigMedia = $bigChannel->medias->first();
        $smallMedia = $smallChannel->medias->first();
        Download::factory()->media($hugeMedia)->logDate(Carbon::create(2022, 8, 11))->create(['counted' => 1_000_000]);
        Download::factory()->media($bigMedia)->logDate(Carbon::create(2022, 8, 10))->create(['counted' => 100_000]);
        Download::factory()->media($smallMedia)->logDate(Carbon::create(2022, 8, 21))->create(['counted' => 1000]);

        $results = Download::downloadsForChannelsDuringPeriod($startDate, $endDate);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(3, $results);

        $expectedResult = [
            [
                'channel_id' => $smallChannel->channel_id,
                'aggregate' => 1000,
            ],
            [
                'channel_id' => $hugeChannel->channel_id,
                'aggregate' => 1000000,
            ],
            [
                'channel_id' => $bigChannel->channel_id,
                'aggregate' => 100000,
            ],
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            $results->toArray()
        );
    }

    /** @test */
    public function it_should_get_only_channel_having_more_than(): void
    {
        $channels = Channel::factory()->count(3)->create();

        $channels->each(fn (Channel $channel) => Media::factory()->channel($channel)->create());

        $august2022 = Carbon::create(2022, 8, 01);
        $startDate = clone $august2022;
        $endDate = (clone $startDate)->endOfMonth();

        $hugeChannel = $channels->get(0);
        $bigChannel = $channels->get(1);
        $smallChannel = $channels->get(2);

        $hugeMedia = $hugeChannel->medias->first();
        $bigMedia = $bigChannel->medias->first();
        $smallMedia = $smallChannel->medias->first();
        Download::factory()->media($hugeMedia)->logDate(Carbon::create(2022, 8, 11))->create(['counted' => 1_000_000]);
        Download::factory()->media($bigMedia)->logDate(Carbon::create(2022, 8, 10))->create(['counted' => 100_000]);
        Download::factory()->media($smallMedia)->logDate(Carbon::create(2022, 8, 21))->create(['counted' => 1000]);

        $results = Download::downloadsForChannelsDuringPeriod($startDate, $endDate, 150_000);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);

        $expectedResult = [
            [
                'channel_id' => $hugeChannel->channel_id,
                'aggregate' => 1000000,
            ],
        ];
        $this->assertEqualsCanonicalizing(
            $expectedResult,
            $results->toArray()
        );
    }

    /** @test */
    public function it_should_get_all_downloads_day_by_day(): void
    {
        // preparing
        $channels = Channel::factory()->count(2)->create();
        $channels->each(fn (Channel $channel) => $this->addMediasToChannel($channel));

        // I want 4 download rows per channel
        $expectedRows = 4;
        $startDate = Carbon::create(2022, 8, 15);
        $endDate = (clone $startDate)->addDays($expectedRows);

        $this->addDownloadsForChannelsMediasDuringPeriod($channels, $startDate, $endDate);

        // test
        $results = Download::downloadsByInterval(
            interval: Download::INTERVAL_PER_DAY,
            startDate: Carbon::create('first day of august 2022', 'Europe/Paris'),
            endDate: Carbon::create('last day of august 2022', 'Europe/Paris')
        );
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount($expectedRows, $results);

        // checking day by day
        $startDate = Carbon::create(2022, 8, 15);
        $index = 0;
        while ($startDate->lessThan($endDate)) {
            $expectedResult = Download::query()
                ->where('log_day', '=', $startDate->toDateString())
                ->sum('counted')
            ;
            $this->assertEquals($expectedResult, $results->get($index)->counted);

            $index++;
            $startDate->addDay();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    public function createAnotherChannelWithDownloads(Carbon $startDate, Carbon $endDate): void
    {
        // creating another channel with downloads
        $anotherChannel = Channel::factory()->hasMedias(3)->create();
        $this->addDownloadsForChannelMediasDuringPeriod($anotherChannel, $startDate, $endDate);
    }
}
