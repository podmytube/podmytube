<?php

namespace App\Traits;

use App\User;

trait BelongsToUser
{
    /**
     * User relationship.
     *
     * @return object the user that own this channel
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
