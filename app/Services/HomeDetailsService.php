<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class HomeDetailsService
{
    public function userContent(User $user): Collection
    {
        return Channel::query()
            ->select('user_id', 'channel_id', 'channel_name', 'podcast_title', 'active')
            ->where('user_id', '=', $user->id())
            ->with([
                'playlists:id,channel_id,active',
                'subscription.plan:id,name,price,slug',
            ])
            ->get()
        ;
    }
}
