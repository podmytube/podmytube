<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Download;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DownloadsTableSeeder extends Seeder
{
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

    public function run()
    {
        if (!App::environment('local')) {
            return true;
        }
        $channel = Channel::byChannelId(static::JEANVIET_CHANNEL_ID);

        $startDate = now()->subdays(40);
        while ($startDate->lessThan(now())) {
            Download::factory()
                ->channel($channel)
                ->logDate($startDate)
                ->create()
            ;
            $startDate->addDay();
        }
    }
}
