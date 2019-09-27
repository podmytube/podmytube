<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Channel;
use App\Thumb;
use Faker\Generator as Faker;

$factory->define(Thumb::class, function (Faker $faker) {
    /**
     * creating fake channel (to have one)
     */
    factory(Channel::class)->create();

    /**
     * Getting one random channel
     */
    $channel=Channel::all()->random();
    
    /**
     * generate an image using loremPixels and storing it into
     * /app/tmp. I need to use basename because filename is something
     * like /app/tmp/07b38eca6266b5cad874444e429c7579.jpg
     * and when I check the size, the storage disk (set in 
     * filesystems.php) is already giving the path.
     */
    $fileName = basename(
        $faker->image("/app/tmp", 1400, 1400, 'nature')
    );

    return [
        'channel_id' => $channel->channel_id,
        'file_name' => $fileName,
        'file_disk' => Thumb::_TEMP_STORAGE_DISK, // where it is stored
        'file_size' => Storage::disk(Thumb::_TEMP_STORAGE_DISK)->size("$fileName"), // its size
    ];    
});
