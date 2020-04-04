<?php

namespace App\Policies;

use App\Channel;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
{
  use HandlesAuthorization;

  public function owns(User $user, Channel $channel): bool
  {
    return $user->userId() == $channel->userId();
  }
}
