<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;

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
