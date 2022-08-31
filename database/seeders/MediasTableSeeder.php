<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MediasTableSeeder extends Seeder
{
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!App::environment('local')) {
            return true;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('medias')->truncate();
        Schema::enableForeignKeyConstraints();

        /** create channel */
        $channel = Channel::byChannelId(self::JEANVIET_CHANNEL_ID);

        // adding known medias to jean viet playlist
        collect(['GCEMmbhfPFU', 'Qd15PfStCxI', 'E0sjQ5FxEHw', '0B-aAnUFO8I', 'v9_ARxG2aVQ', '9FcOlmabjZA', '3N9QMMYazdk', '0PuR-R6p2cg', 'JY1WUzazY98'])
            ->each(function (string $mediaId) use ($channel): void {
                Media::factory()
                    ->channel($channel)
                    ->grabbedAt(now()->subday())
                    ->create([
                        'media_id' => $mediaId,
                    ])
                ;
            })
        ;
    }
}
