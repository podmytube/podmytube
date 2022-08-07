<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Media;

trait HasManyMedias
{
    /**
     * define the relationship between one channel and its medias.
     */
    public function medias()
    {
        return $this->HasMany(Media::class, 'channel_id');
    }
}
