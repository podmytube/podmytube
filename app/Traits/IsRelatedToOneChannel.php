<?php

declare(strict_types=1);

namespace App\Traits;

trait IsRelatedToOneChannel
{
    public static function byChannelId(string $channelId): ?self
    {
        return self::where('channel_id', '=', $channelId)->first();
    }
}
