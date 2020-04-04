<?php

/**
 *   this class is the model class for the User table
 *
 */

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model Class
 *
 */
class User extends Authenticatable
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
  protected $fillable = ['name', 'email', 'password', 'language'];

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
    return $this->user_id;
  }
}
