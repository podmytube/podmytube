<?php

use App\Channel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment(['local']) && env('DB_CONNECTION') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Channel::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            /**
             * creating my own user
             */
            $data = [
                [
                    'channel_id' => 'UCTEzSp8NmvyjvXUj-eNYVuw',
                    'user_id' => 1,
                    'channel_name' => 'La goupilation',
                ],
                [
                    'channel_id' => 'UCw6bU9JT_Lihb2pbtqAUGQw',
                    'user_id' => 2,
                    'channel_name' => 'Frederick Tyteca',
                ],
            ];
            /**
             * insert will set only the data specified
             * create will set the timestamps also
             */
            Channel::insert($data);
        }
    }
}
