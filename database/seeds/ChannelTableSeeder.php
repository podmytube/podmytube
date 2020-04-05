<?php

use App\Channel;
use Carbon\Carbon;
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
        if (!App::environment('production')) {
            DB::table('channels')->delete();

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
