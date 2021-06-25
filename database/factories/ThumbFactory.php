<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Thumb;

$factory->define(Thumb::class, function ($faker, $attributes) {
    return [
        'file_name' => $attributes['file_name'] ?? $faker->word() . '.jpg',
        'file_disk' => $attributes['file_disk'] ?? Thumb::LOCAL_STORAGE_DISK, // where it is stored
        'file_size' => $attributes['file_size'] ?? rand(25000, 50000),
        'coverable_type' => $attributes['coverable_type'] ?? null,
        'coverable_id' => $attributes['coverable_id'] ?? null,
    ];
});
