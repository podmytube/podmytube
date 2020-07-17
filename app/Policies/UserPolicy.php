<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    public function view(User $authenticatedUser, User $userTocheck): bool
    {
        return $authenticatedUser->is($userTocheck);
    }

    public function edit(User $authenticatedUser, User $userTocheck): bool
    {
        return $authenticatedUser->is($userTocheck);
    }

    public function update(User $authenticatedUser, User $userTocheck): bool
    {
        return $authenticatedUser->is($userTocheck);
    }
}