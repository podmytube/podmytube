<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Category;
use App\Channel;
use App\Language;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    public $model = Channel::class;

    public function definition(): array
    {
        return [
            'channel_id' => 'FAKE-' . fake()->regexify('[a-zA-Z0-9]{6}'),
            'user_id' => User::factory(),
            'channel_name' => fake()->words('2', true),
            'podcast_title' => fake()->words('3', true),
            'podcast_copyright' => fake()->words('4', true),
            'authors' => 'John Lorem',
            'email' => 'john@loremipsum.com',
            'link' => 'https://loremipsum.com',
            'category_id' => Category::factory(),
            'language_id' => Language::factory(),
            'explicit' => false,
            'active' => true,
            'channel_createdAt' => now()->subDays(5),
            'channel_updatedAt' => now()->subDays(3),
            'podcast_updatedAt' => now()->subDays(2),
            'accept_video_by_tag' => null,
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => null,
            'description' => <<<'EOD'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
Duis quis velit dictum mauris lobortis porta et sollicitudin ante.
EOD,
        ];
    }
}
