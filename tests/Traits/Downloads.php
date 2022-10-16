<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Exceptions\ChannelHasNoMediaException;
use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Downloads
{
    public function addDownloadsForMediaDuringPeriod(Media $media, Carbon $startDate, Carbon $endDate): int
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
    public function addDownloadsForChannelMediasDuringPeriod(Channel $channel, Carbon $startDate, Carbon $endDate): int
    {
        throw_unless(
            $channel->medias->count(),
            new ChannelHasNoMediaException("Channel {$channel->youtube_id} has no media.")
        );

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

    public function addDownloadsForChannelsMediasDuringPeriod(Collection $channels, Carbon $startDate, Carbon $endDate): int
{
    $totalDownloads = 0;
    while ($startDate->lessThan($endDate)) {
        $totalDownloads = $channels->reduce(function (?int $carry, Channel $channel) use ($startDate): int {
            $downloads = $channel->medias->reduce(function ($carry, Media $media) use ($startDate) {
                $download = Download::factory()
                    ->media($media)
                    ->logDate($startDate)
                    ->create()
                ;

                return $carry + $download->counted;
            });

            return $carry + $downloads;
        }, $totalDownloads);

        $startDate->addDay();
    }

    return $totalDownloads;
}
}
