<?php

namespace App\Policies;

use App\Channel;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Response;

class ChannelPolicy
{
    use HandlesAuthorization;
    public function __construct()
    {
        dump(__CLASS__ . "-" . __FUNCTION__);
    }

    public function view(User $user, Channel $channel): bool
    {
        dd(__CLASS__ . '-' . __FILE__);
        return $user->userId() == $channel->userId()
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    public function index(User $user, Channel $channel): bool
    {
        dd(__CLASS__ . '-' . __FILE__);
    }

    public function update(User $user, Channel $channel): bool
    {
        return $user->userId() == $channel->userId()
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }
}
