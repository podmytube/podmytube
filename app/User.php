<?php

/**
 *   this class is the model class for the User table
 */

namespace App;

use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model Class
 */
class User extends Authenticatable implements HasLocalePreference
{
    use Notifiable;

    /**
     * the way to specify users.user_id is the key (and not users.id)
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'language', 'newsletter'];

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

    public function preferredLocale()
    {
        return $this->language;
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
        return $this->id() === 1;
    }
}
