<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApiKey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('api_keys')->delete();

        $data = [
            [
                'apikey' => 'AIzaSyB_iYiVpA9GWst4Mlr3-qcmlvL8dTLIZxo',
                'comment' => 'mangeurdechamois',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyB0Z5tK4-vzk8B-pICDBcWVIwzkg_-pzyk',
                'comment' => 'captainbouflamoule',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk',
                'comment' => 'naindormeur',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyAbTyKQH1vVLYjWs_8lMRzTWq57ZbTg0vc',
                'comment' => 'vinaigretteetchoufleur',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyAOd9_Gc3qbbYJKSsLfy0sYBnVqSQiE-_A',
                'comment' => 'ohlabellechevre',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyBzbKh2MCcZz2DYKiRKaBjAUUC8RkTxLsk',
                'comment' => 'lundesseptnains',
                'created_at' => Carbon::now(),
            ],
            [
                'apikey' => 'AIzaSyAH-O9meZ0sDrh6iZZoBLCgB3rLCKUuMHg',
                'comment' => 'lundesseptsalopards',
                'created_at' => Carbon::now(),
            ],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                ]);
            },
            $data
        );

        ApiKey::insert($data);
    }
}
