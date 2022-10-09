<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Media;
use Carbon\Carbon;
use Database\Factories\Traits\HasChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    use HasChannel;

    public function definition(): array
    {
        return [
            'media_id' => fake()->regexify('[a-zA-Z0-9-_]{4}'),
            'channel_id' => Channel::factory(),
            'title' => fake()->words(2, true),
            'description' => <<<'EOT'
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi utt.
EOT,
            'duration' => 0,
            'length' => 0,
            'published_at' => now(),
            'status' => Media::STATUS_NOT_DOWNLOADED,
        ];
    }

    public function grabbedAt(Carbon $grabbedAt): static
    {
        return $this->state(
            [
                'length' => fake()->numberBetween(100, 900000),
                'duration' => fake()->numberBetween(0, 65535), // duration is smallint
                'grabbed_at' => $grabbedAt,
            ]
        );
    }

    public function uploadedByUser(): static
    {
        return $this->state(['uploaded_by_user' => true]);
    }

    public function channel(Channel $channel): static
    {
        return $this->state(
            [
                'channel_id' => $channel->youtube_id,
            ]
        );
    }
}
