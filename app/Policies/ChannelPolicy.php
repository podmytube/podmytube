<?php

namespace App\Policies;

use App\User;
use App\Channel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
{
    use HandlesAuthorization;

    public function owns(User $user, Channel $channel): bool
    {
        return $user->userId() == $channel->userId();
    }
}
