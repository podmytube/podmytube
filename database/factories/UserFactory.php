<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstname(),
            'lastname' => fake()->lastname(),
            'email' => fake()->email(),
            'password' => '$2y$10$rIo.zLS88CNtH66fSa4DOOYkzPIq8RGkS.DqyG/AoYOUI272HD5Sa', // secret
            'remember_token' => Str::random(10),
            'newsletter' => true,
            'superadmin' => false,
            'dont_warn_exceeding_quota' => false,
            'referral_code' => fake()->bothify('????####'),
        ];
    }

    public function withReferralCode(string $referralCode): static
    {
        return $this->state([
            'referral_code' => $referralCode,
        ]);
    }

    public function withReferrer(User $user): static
    {
        return $this->state([
            'referrer_id' => $user->id(),
        ]);
    }

    public function verifiedAt(Carbon $verifiedAt): static
    {
        return $this->state([
            'email_verified_at' => $verifiedAt,
        ]);
    }
}
