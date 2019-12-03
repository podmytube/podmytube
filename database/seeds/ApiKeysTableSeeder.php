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
            ['apikey' => 'AIzaSyB_iYiVpA9GWst4Mlr3-qcmlvL8dTLIZxo', 'comment' => 'mangeurdechamois', 'environment' => ApiKey::_PROD_ENV, 'created_at' => Carbon::now()],
            ['apikey' => 'AIzaSyB0Z5tK4-vzk8B-pICDBcWVIwzkg_-pzyk', 'comment' => 'captainbouflamoule', 'environment' => ApiKey::_PROD_ENV, 'created_at' =>  Carbon::now()],
            ['apikey' => 'AIzaSyDu5_d6Etu8N0biP6zfDN4FNe675FcgRkk', 'comment' => 'naindormeur', 'environment' => ApiKey::_LOCAL_ENV, 'created_at' =>  Carbon::now()],
        ];

        ApiKey::insert($data);
    }
}
