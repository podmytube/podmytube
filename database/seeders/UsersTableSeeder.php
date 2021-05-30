<?php

namespace Database\Seeders;

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
        DB::table('users')->delete();

        $data = [
            [
                'firstname' => 'Fred',
                'email' => 'frederick@podmytube.com',
                'password' => '$2y$10$c5.Md.ZOCYFv70pPv/3nAeNSsXzV3ttFguIu.GzEFYhaijHh988se',
            ],
            [
                'firstname' => 'another fred',
                'email' => 'frederick@tyteca.net',
                'password' => '$2y$10$pDoZavewGcqHU93YwOR3zOOzDKICchCQtirhhKHCV/FVxY55yFNA.',
            ],
        ];
        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'user_id' => $index++,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            },
            $data
        );

        User::insert($data);
    }
}
