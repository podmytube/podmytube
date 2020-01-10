<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Modules\PeriodsHelper;

$factory->define(App\Media::class, function ($faker, $attributes) {
    /** specific period may be specified */
    $month = $attributes['month'] ?? null;
    $year = $attributes['year'] ?? null;

    /** preparing published and gabbed at period */
    $periodObj = PeriodsHelper::create($month,$year);
    $publishedAt = $faker->dateTimeBetween($periodObj->startDate(), $periodObj->endDate());
    $grabbedAt = $faker->dateTimeBetween($publishedAt, $periodObj->endDate());

    /** returning our nice new media */
    return [
        'media_id' => $faker->regexify('[a-zA-Z0-9-_]{8}'),
        'channel_id' => $attributes['channel_id'] ?? $faker->regexify('[a-zA-Z0-9-_]{24}'),
        'title' => $faker->sentence($nbWords = "3", $variableNbWords = true),
        'description' => $faker->text(300),
        'length' => $faker->numberBetween(1000, 40000),
        'duration' => $faker->randomNumber(),
        'published_at' => $publishedAt,
        'grabbed_at' => $grabbedAt,
        'active' => true,
    ];
});
