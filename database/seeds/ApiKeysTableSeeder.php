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
        DB::table('api_keys')->delete();

        $data = [
            ['id' => 1, 'apikey' => 'AIzaSyB_iYiVpA9GWst4Mlr3-qcmlvL8dTLIZxo', 'comment' => 'mangeurdechamois', 'created_at' => Carbon::now()],
            ['id' => 2, 'apikey' => 'AIzaSyB0Z5tK4-vzk8B-pICDBcWVIwzkg_-pzyk', 'comment' => 'captainbouflamoule', 'created_at' =>  Carbon::now()],
        ];

        ApiKey::insert($data);
    }
}
