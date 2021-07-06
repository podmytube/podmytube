<?php

declare(strict_types=1);

namespace App\Policies;

use App\Channel;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
{
    use HandlesAuthorization;

    public function any(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function view(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function update(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function addMedia(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function updateMedia(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }

    public function delete(User $user, Channel $channel): bool
    {
        return $channel->user->is($user);
    }
}
