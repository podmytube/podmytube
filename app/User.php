<?php

/**
 *   this class is the model class for the User table
 */

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model Class
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * the way to specify users.user_id is the key (and not users.id)
     */
    protected $primaryKey = 'user_id';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * this function define the relationship between one User and his channels
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

    public function isSuperAdmin()
    {
        return $this->superadmin === 1;
    }

    public static function byEmail(string $email): ?self
    {
        return self::where('email', '=', $email)->first();
    }
}
