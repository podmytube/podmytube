<?php

use Faker\Generator as Faker;

$factory->define(App\Thumbs::class, function (Faker $faker) {
    static $channels = [
        'UC0E0g7aXin1YGKO0QTwhkZw', // Lola Lol
    ];

    $channel_id = $channels[rand(0,count($channels)-1)];
    $file_name = "thumb.jpg"; // old 
    $file_disk = 'thumbs'; // disk as defined in config/filesystems.php
    return [
        'channel_id' => 'UC0E0g7aXin1YGKO0QTwhkZw', // getting one real channel is more simple
        'file_name' => $file_name,
        'file_disk' => $file_disk,
        'file_size' => Storage::disk($file_disk)->size('UC0E0g7aXin1YGKO0QTwhkZw/'.$file_name),        
    ];
});
