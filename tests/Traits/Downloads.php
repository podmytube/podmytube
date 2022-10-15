<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Exceptions\ChannelHasNoMediaException;
use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;

trait Downloads
{
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
}
