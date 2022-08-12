<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'log_day' => now()->toDateString(),
            'channel_id' => Channel::factory(),
            'media_id' => Media::factory(),
            'counted' => fake()->numberBetween(0, 100),
        ];
    }

    public function logDate(Carbon $date): static
    {
        return $this->state(['log_day' => $date->toDateString()]);
    }

    public function channel(Channel $channel): static
    {
        return $this->state(['channel_id' => $channel->channel_id]);
    }

    public function media(Media $media): static
    {
        return $this->state([
            'media_id' => $media->id,
            'channel_id' => $media->channel->channel_id,
        ]);
    }
}
