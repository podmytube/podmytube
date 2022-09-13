<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        $data = [
            [
                'firstname' => 'Fred',
                'email' => 'frederick@podmytube.com',
                'password' => '$2y$10$NZouI76/3YhdnrkRXT3ee.1MaHA3zvg3TDKS07vobYfSV0rsHnEN2',
                'superadmin' => true,
            ],
            [
                'firstname' => 'another fred',
                'email' => 'frederick@tyteca.net',
                'password' => '$2y$10$pDoZavewGcqHU93YwOR3zOOzDKICchCQtirhhKHCV/FVxY55yFNA.',
                'superadmin' => false,
            ],
        ];
        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'user_id' => $index++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            },
            $data
        );

        User::insert($data);
    }
}
