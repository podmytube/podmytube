<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\Vignette;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThumbsTableSeeder extends Seeder
{
    public function run()
    {
        if (!App::environment('local')) {
            return false;
        }

        DB::table('thumbs')->delete();

        /** getting channel */
        $channel = Channel::byChannelId(ChannelsTableSeeder::JEANVIET_CHANNEL_ID);

        $filename = 'jeanviet.jpg';
        $filepath = $channel->channelId() . DIRECTORY_SEPARATOR . $filename;

        // copying lorem image to its new fake location to be tested
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)->put(
            $filepath,
            file_get_contents(base_path('tests/Fixtures/images/jeanviet.jpg'))
        );
        $filesize = Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filepath);

        /** create thumb */
        $thumb = Thumb::create([
            'file_name' => $filename,
            'file_disk' => Thumb::LOCAL_STORAGE_DISK,
            'file_size' => $filesize,
            'created_at' => now(),
            'updated_at' => now(),
            'coverable_type' => get_class($channel),
            'coverable_id' => $channel->channelId(),
        ]);

        Vignette::fromThumb($thumb)->makeIt()->saveLocally();
    }
}
