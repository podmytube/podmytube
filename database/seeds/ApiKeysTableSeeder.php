<?php

use App\ApiKey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ApiKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('DB_CONNECTION') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            ApiKey::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            DB::table('api_keys')->delete();
        }

        $data = [
            [
                'apikey' => 'AIzaSyB_iYiVpA9GWst4Mlr3-qcmlvL8dTLIZxo',
                'comment' => 'mangeurdechamois',
                'environment' => ApiKey::PROD_ENV,
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyB0Z5tK4-vzk8B-pICDBcWVIwzkg_-pzyk',
                'comment' => 'captainbouflamoule',
                'environment' => ApiKey::PROD_ENV,
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk',
                'comment' => 'naindormeur',
                'environment' => ApiKey::LOCAL_ENV,
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyC0NDrm3rC9BLI9bWLCLZeGnynqu79IySA',
                'comment' => 'johnsleepwalker',
                'environment' => ApiKey::PROD_ENV,
                'created_at' => Carbon::now(),
            ],
        ];

        ApiKey::insert($data);
    }
}
