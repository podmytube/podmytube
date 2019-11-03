<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class usersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('prod')) {
            
            DB::table('users')->delete();
            
            /**
             * creating my own user
             */
            $data = [
                [
                    'user_id' => 1,
                    'name' => 'Fred',
                    'email' => 'frederick@podmytube.com',
                    'password' => '$2y$10$c5.Md.ZOCYFv70pPv/3nAeNSsXzV3ttFguIu.GzEFYhaijHh988se',
                ],
            ];
            User::insert($data);
        }
    }
}
