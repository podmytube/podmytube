<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // These seeders are used every time
        Model::unguard();

        $requiredSeeders = [
            ApiKeysTableSeeder::class,
            PlansTableSeeder::class,
            CategoriesTableSeeder::class,
            StripePlansTableSeeder::class,
            LanguagesTableSeeder::class,
        ];

        $additionalSeeders = [];
        if (App::environment(['local'])) {
            $additionalSeeders = [
                UsersTableSeeder::class,
                ChannelsTableSeeder::class,
                PlaylistsTableSeeder::class,
                MediasTableSeeder::class,
                DownloadsTableSeeder::class,
                ThumbsTableSeeder::class,
            ];
        }

        $seeders = array_merge($requiredSeeders, $additionalSeeders);
        $this->call($seeders);
        Model::reguard();
    }
}
