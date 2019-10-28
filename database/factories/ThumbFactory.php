<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

$factory->define(App\Thumb::class, function ($faker, $attributes) {
    if (isset($attributes['channel_id'])) {
        $channelId = $attributes['channel_id'];
    } else {
        $channelId = $faker->regexify('[a-zA-Z0-9-_]{24}');
    }

    

    Storage::disk(App\Thumb::_LOCAL_STORAGE_DISK)->makeDirectory($channelId, intval('0664', 8), true);
    
    
    $fileName = basename(
        $faker->image(storage_path("app/public/thumbs/$channelId"), 600, 600, 'nature')
    );

    return [
        'channel_id' => $channelId,
        'file_name' => $fileName,
        'file_disk' => App\Thumb::_LOCAL_STORAGE_DISK, // where it is stored
        'file_size' => Storage::disk(App\Thumb::_LOCAL_STORAGE_DISK)->size("$channelId/$fileName"), // its size
    ];
});
