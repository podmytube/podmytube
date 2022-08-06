<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory(),
            'youtube_playlist_id' => 'FAKE-' . fake()->regexify('[a-zA-Z0-9]{10}'),
            'title' => fake()->words('3', true),
            'description' => <<<'EOD'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
Pellentesque eget tristique orci. Proin convallis malesuada mauris. 
Donec ultricies magna odio, eget vulputate ligula molestie vitae. 
Duis quis velit dictum mauris lobortis porta et sollicitudin ante.
EOD,
            'active' => false,
        ];
    }
}
