<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
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
        $media = Media::factory()->channel($channel)->create();

        $startDate = now()->subdays(40);
        while ($startDate->lessThan(now())) {
            Download::factory()
                ->channel($channel)
                ->media($media)
                ->logDate($startDate)
                ->create()
            ;
            $startDate->addDay();
        }
    }
}
