<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Channel;

trait BelongsToChannel
{
    /**
     * define the relationship between media and its channel.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }
}
