<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use Database\Factories\Traits\HasChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistFactory extends Factory
{
    use HasChannel;

    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory(),
            'youtube_playlist_id' => 'FAKE-' . fake()->regexify('[a-zA-Z0-9]{8}'),
            'title' => fake()->words('3', true),
            'description' => <<<'EOD'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
Pellentesque eget tristique orci. Proin convallis malesuada mauris. 
EOD,
            'active' => false,
        ];
    }
}
