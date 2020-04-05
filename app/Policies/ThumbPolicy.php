<?php

namespace App\Policies;

use App\Channel;
use App\Thumb;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThumbPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function update(User $user, Thumb $thumb): bool
    {
        return $thumb->channel->user->is($user);
    }
}
