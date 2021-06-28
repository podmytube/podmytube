<?php

declare(strict_types=1);

namespace App\Policies;

use App\Playlist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaylistPolicy
{
    use HandlesAuthorization;

    public function any(User $user, Playlist $playlist): bool
    {
        return $playlist->channel->user->is($user);
    }

    public function view(User $user, Playlist $playlist): bool
    {
        return $playlist->channel->user->is($user);
    }

    public function update(User $user, Playlist $playlist): bool
    {
        return $playlist->channel->user->is($user);
    }

    public function addMedia(User $user, Playlist $playlist): bool
    {
        return $playlist->channel->user->is($user);
    }

    public function updateMedia(User $user, Playlist $playlist): bool
    {
        return $playlist->channel->user->is($user);
    }
}
