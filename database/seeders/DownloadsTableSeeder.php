<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Download;

class DownloadsTableSeeder extends LocalSeeder
{
    public function run(): void
    {
        $this->truncateTables('downloads');

        $jeanVietChannel = Channel::byChannelId(static::JEANVIET_CHANNEL_ID);
        $ftytecaChannel = Channel::byChannelId(static::FTYTECA_CHANNEL_ID);

        $startDate = now()->subdays(40);
        while ($startDate->lessThan(now())) {
            Download::factory()
                ->channel($jeanVietChannel)
                ->media($jeanVietChannel->medias->first())
                ->logDate($startDate)
                ->create()
            ;

            if (fake()->boolean(20)) {
                Download::factory()
                    ->channel($ftytecaChannel)
                    ->media($ftytecaChannel->medias->first())
                    ->logDate($startDate)
                    ->create()
                ;
            }

            $startDate->addDay();
        }
    }
}
