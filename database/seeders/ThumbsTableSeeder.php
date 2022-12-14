<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Thumb;
use App\Modules\Vignette;
use Illuminate\Support\Facades\Storage;

class ThumbsTableSeeder extends LocalSeeder
{
    public function run(): void
    {
        $this->truncateTables('thumbs');

        /** getting channel */
        $channel = Channel::byChannelId(static::JEANVIET_CHANNEL_ID);

        $filename = 'jeanviet.jpg';
        $filepath = $channel->channelId() . DIRECTORY_SEPARATOR . $filename;

        // copying lorem image to its new fake location to be tested
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)->put(
            $filepath,
            file_get_contents(fixtures_path('/images/jeanviet.jpg'))
        );
        $filesize = Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filepath);

        /** create thumb */
        $thumb = Thumb::create([
            'file_name' => $filename,
            'file_disk' => Thumb::LOCAL_STORAGE_DISK,
            'file_size' => $filesize,
            'created_at' => now(),
            'updated_at' => now(),
            'coverable_type' => $channel->morphedName(),
            'coverable_id' => $channel->channelId(),
        ]);

        Vignette::fromThumb($thumb)->makeIt()->saveLocally();
    }
}
