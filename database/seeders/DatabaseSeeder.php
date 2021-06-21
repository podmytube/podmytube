<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * These seeders are used every time
         */
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
                
            ];
        }

        $seeders = array_merge($requiredSeeders, $additionalSeeders);
        $this->call($seeders);
        Model::reguard();
    }
}
