<?php

use App\Channel;
use Illuminate\Database\Seeder;

class ChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('prod')) {

            DB::table('channels')->delete();

            /**
             * creating my own user
             */
            $data = [
                [
                    'channel_id' => 'UCTEzSp8NmvyjvXUj-eNYVuw',
                    'user_id' => 1,
                    'channel_name' => 'La goupilation'
                ],
            ];
            Channel::insert($data);
        }
    }
}
