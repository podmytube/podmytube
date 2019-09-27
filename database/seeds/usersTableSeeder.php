<?php

use App\User;
use Illuminate\Database\Seeder;

class usersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment(['dev', 'local', 'rec', 'micromania', 'testing'])) {
            /**
             * emptying table User
             */
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            User::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

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
