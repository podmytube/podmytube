<?php

namespace App\Traits;

use App\Media;

trait HasManyMedias
{
    /**
     * define the relationship between one channel and its medias
     */
    public function medias()
    {
        return $this->HasMany(Media::class, 'channel_id');
    }
}
