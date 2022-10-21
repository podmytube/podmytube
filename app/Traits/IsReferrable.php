<?php

declare(strict_types=1);

namespace App\Traits;

trait IsReferrable
{
    public function referrer()
    {
        return $this->belongsTo(self::class, 'referrer_id');
    }

    public static function createReferralCode(): string
    {
        return fake()->bothify('????####');
    }

    public function referralLink(): string
    {
        return route('register', ['referral_code' => $this->referral_code]);
    }

    public static function byReferralCode(string $referralCode): ?self
    {
        return self::query()->where('referral_code', '=', $referralCode)->first();
    }
}
