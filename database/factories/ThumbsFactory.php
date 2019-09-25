<?php

use App\Thumbs;
use Faker\Generator as Faker;

$factory->define(App\Thumbs::class, function (Faker $faker) {



    return [
        'channel_id' => $faker->regexify('[a-zA-Z0-9-_]{24}'), // getting one real channel is more simple
        'file_name' => $faker->image('/tmp',1400,1400,'nature'),
        'file_disk' => Thumbs::_FILE_DISK,
        'file_size' => Storage::disk($file_disk)->size('UC0E0g7aXin1YGKO0QTwhkZw/'.$file_name),        
    ];
});
