<?php

namespace App\Traits;

use App\Thumb;

trait HasOneThumb
{
    public function thumb()
    {
        return $this->HasOne(Thumb::class, 'channel_id');
    }
}
