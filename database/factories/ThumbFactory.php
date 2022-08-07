<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Thumb;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThumbFactory extends Factory
{
    public function definition(): array
    {
        return [
            'file_name' => fake()->word() . '.jpg',
            'file_disk' => Thumb::LOCAL_STORAGE_DISK, // where it is stored
            'file_size' => random_int(25000, 50000),
            'coverable_type' => null,
            'coverable_id' => null,
        ];
    }
}
