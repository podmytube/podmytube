<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('production')) {
            DB::table('users')->delete();

            $data = [
                [
                    'user_id' => 1,
                    'name' => 'Fred',
                    'email' => 'frederick@podmytube.com',
                    'password' =>
                        '$2y$10$c5.Md.ZOCYFv70pPv/3nAeNSsXzV3ttFguIu.GzEFYhaijHh988se',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'user_id' => 2,
                    'name' => 'another fred',
                    'email' => 'frederick@tyteca.net',
                    //chat
                    'password' =>
                        '$2y$10$/6YHjNFwNuvXqq7023c3NedYMIi1vcjMj8r1UzIYmrBl5y.zVI.m2',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];
            User::insert($data);
        }
    }
}
