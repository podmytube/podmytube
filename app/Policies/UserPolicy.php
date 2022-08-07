<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
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
