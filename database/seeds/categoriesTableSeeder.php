<?php

use Illuminate\Database\Seeder;

class categoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(['parent_id' => 0, 'name' => 'Arts']); // 1
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Design']);
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Fashion &amp; Beauty']);
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Food']);
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Literature']); // 5
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Performing Arts']);
        DB::table('categories')->insert(['parent_id' => 1, 'name' => 'Visual Arts']);

        DB::table('categories')->insert(['parent_id' => 0, 'name' => 'Business']); // 8
        DB::table('categories')->insert(['parent_id' => 8, 'name' => 'Business News']);
        DB::table('categories')->insert(['parent_id' => 8, 'name' => 'Careers']); // 10
        DB::table('categories')->insert(['parent_id' => 8, 'name' => 'Investing']);
        DB::table('categories')->insert(['parent_id' => 8, 'name' => 'Management &amp; Marketing']);
        DB::table('categories')->insert(['parent_id' => 8, 'name' => 'Shopping']);

        DB::table('categories')->insert(['parent_id' => 0, 'name' => 'Comedy']); //14
        
    }
}
