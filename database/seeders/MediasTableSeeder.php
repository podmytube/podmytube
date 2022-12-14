<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Media;
use Carbon\Carbon;

class MediasTableSeeder extends LocalSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateTables('medias');

        $channel = Channel::byChannelId(self::JEANVIET_CHANNEL_ID);

        // adding known medias to jean viet playlist
        collect(['GCEMmbhfPFU', 'Qd15PfStCxI', 'E0sjQ5FxEHw', '0B-aAnUFO8I', 'v9_ARxG2aVQ', '9FcOlmabjZA', '3N9QMMYazdk', '0PuR-R6p2cg', 'JY1WUzazY98'])
            ->each(
                fn (string $mediaId) => Media::factory()
                    ->channel($channel)
                    ->grabbedAt(Carbon::createFromDate(2022, 8, fake()->numberBetween(1, 31)))
                    ->create([
                        'media_id' => $mediaId,
                    ])
            )
        ;

        Media::factory()
            ->channel(Channel::byChannelId(self::FTYTECA_CHANNEL_ID))
            ->grabbedAt(now()->subday())
            ->create()
        ;
    }
}
