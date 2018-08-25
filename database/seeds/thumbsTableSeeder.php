<?php

use Illuminate\Database\Seeder;

use App\Thumbs;

use App\Channel;

use Carbon\Carbon;

class thumbsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // getting channels list
        $channels = Channel::select('channel_id')->get();
        
        // array we are going to insert
        $results = [];

        // loop on each channel
        foreach ($channels as $channel) {

            // only if channel has no thumb
            if (Thumbs::where('channel_id', $channel->channel_id)->doesntExist()) {
            // relative file path is still the same channel_id/thumb.jpg
                $file_name = 'thumb.jpg';
                $file_disk = 'thumbs';
                $thumb_path = $channel->channel_id . '/' . $file_name;

            // moving thumb to the new location
                Storage::disk($file_disk)->put($thumb_path, Storage::disk('old_thumbs')->get($thumb_path));

            // storing new/updated entry in db
                $results[] = [
                    'channel_id' => $channel->channel_id,
                    'file_name' => $file_name,
                    'file_disk' => $file_disk,
                    'file_size' => \Storage::disk($file_disk)->size($thumb_path),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

        }
        // inserting array
        Thumbs::insert($results);
    }
}
