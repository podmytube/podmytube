<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

$factory->define(App\Thumb::class, function ($faker, $attributes) {

    $channelId = $attributes['channel_id'] ?? $faker->regexify('[a-zA-Z0-9-_]{24}');

    Storage::disk(App\Thumb::_LOCAL_STORAGE_DISK)->makeDirectory($channelId, intval('0664', 8), true);

    $fileName = $faker->regexify('[a-zA-Z0-9-_]{6}').'jpg';
    $fileSize = $faker->randomNumber();
    if (isset($attributes["withRealImage"]) && $attributes["withRealImage"] === true) {
        $fileName = basename(
            $faker->image(storage_path("app/public/thumbs/$channelId"), 600, 600, 'nature')
        );
        $fileSize = Storage::disk(App\Thumb::_LOCAL_STORAGE_DISK)->size("$channelId/$fileName");
    }

    return [
        'channel_id' => $channelId,
        'file_name' => $fileName,
        'file_disk' => App\Thumb::_LOCAL_STORAGE_DISK, // where it is stored
        'file_size' => $fileSize
    ];
});
