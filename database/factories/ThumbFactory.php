<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Thumb;

$factory->define(Thumb::class, function ($faker, $attributes) {

    
    Storage::disk(Thumb::LOCAL_STORAGE_DISK)
        ->makeDirectory($channelId, intval('0664', 8), true);

    $fileName = $faker->regexify('[a-zA-Z0-9-_]{6}') . '.jpg';
    $filePath = $channelId . '/' . $fileName;

    /**
     * creating fake filename from sample one in fixtures path.
     * this is fast enough to be tested quickly.
     */
    Storage::disk(Thumb::LOCAL_STORAGE_DISK)
        ->put($filePath, file_get_contents(base_path('tests/fixtures/images/sampleThumb.jpg')));
    $fileSize = Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filePath);

    return [
        'file_name' => $fileName,
        'file_disk' => Thumb::LOCAL_STORAGE_DISK, // where it is stored
        'file_size' => $fileSize,
        'coverable_type' => $attributes['coverable_type'] ?? null,
        'coverable_id' => $attributes['coverable_id'] ?? null,
    ];
});
