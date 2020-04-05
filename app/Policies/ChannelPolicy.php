<?php

namespace App\Policies;

use App\Channel;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function update(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }
}
