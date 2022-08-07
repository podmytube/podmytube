<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Channel;
use App\Models\Thumb;
use App\Models\User;
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
