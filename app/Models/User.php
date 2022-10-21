<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\IsReferrable;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

/**
 * User Model Class.
 *
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property Carbon email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property bool   $newsletter
 * @property bool   $superadmin
 * @property string $stripe_id
 * @property bool   $dont_warn_exceeding_quota
 * @property string $referral_code
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Impersonate;
    use HasFactory;
    use IsReferrable;
    use Notifiable;
    use SoftDeletes;

    /**
     * the way to specify users.user_id is the key (and not users.id).
     */
    protected $primaryKey = 'user_id';

    protected $guarded = [];

    protected $casts = [
        'newsletter' => 'boolean',
        'superadmin' => 'boolean',
        'dont_warn_exceeding_quota' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * this function define the relationship between one User and his channels.
     */
    public function channels()
    {
        return $this->HasMany(Channel::class, 'user_id');
    }

    public function userId()
    {
        return $this->id();
    }

    public function id()
    {
        return $this->user_id;
    }

    public function isSuperAdmin(): bool
    {
        return $this->superadmin === true;
    }

    public static function byEmail(string $email): ?self
    {
        return self::where('email', '=', $email)->first();
    }

    public static function byStripeId(string $stripeId): ?self
    {
        return self::where('stripe_id', '=', $stripeId)->first();
    }

    public function scopeNewsletter(Builder $query)
    {
        return $query->where('newsletter', '=', 1);
    }

    public static function whoWantNewsletter(): Collection
    {
        return self::newsletter()
            ->select('email', 'firstname', 'lastname')
            ->whereHas('channels', function (Builder $query): void {
                $query->where('active', '=', 1);
            })
            ->get()
        ;
    }

    public function getNameAttribute()
    {
        $result = $this->firstname;

        if ($this->lastname) {
            $result .= ' ' . $this->lastname;
        }

        return $result;
    }

    public function wantToBeWarnedForExceedingQuota(): bool
    {
        return $this->dont_warn_exceeding_quota === false;
    }
}
