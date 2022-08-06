<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
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
            'length' => 0,
            'duration' => 0,
            'published_at' => now(),
            'grabbed_at' => null,
            'status' => Media::STATUS_NOT_DOWNLOADED,
            'deleted_at' => null,
            'uploaded_by_user' => false,
        ];
    }

    public function channel(Channel $channel): static
    {
        return $this->state(['channel_id' => $channel->channel_id]);
    }

    public function grabbedAt(Carbon $grabbedAt): static
    {
        return $this->state(
            [
                'length' => 355,
                'duration' => 2500,
                'grabbed_at' => $grabbedAt,
            ]
        );
    }
}
