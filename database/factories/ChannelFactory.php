<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Language;
use App\Models\User;
use Carbon\Carbon;
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
            'authors' => 'John Lorem',
            'email' => 'john@loremipsum.com',
            'link' => 'https://loremipsum.com',
            'category_id' => Category::factory(),
            'language_id' => Language::factory(),
            'explicit' => false,
            'active' => true,
            'description' => <<<'EOD'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum fermentum ex luctus posuere. 
Duis quis velit dictum mauris lobortis porta et sollicitudin ante.
EOD,
        ];
    }

    public function user(User $user): static
    {
        return $this->state(['user_id' => $user->user_id]);
    }

    public function category(Category $category): static
    {
        return $this->state(['category_id' => $category->id]);
    }

    public function language(Language $language): static
    {
        return $this->state(['language_id' => $language->id]);
    }

    public function active(bool $active = true): static
    {
        return $this->state(['active' => $active]);
    }

    public function createdAt(Carbon $date): static
    {
        return $this->state(['created_at' => $date]);
    }
}
