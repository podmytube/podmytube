<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Thumb;

trait HasOneThumb
{
    public function thumb()
    {
        return $this->HasOne(Thumb::class, 'channel_id');
    }
}
