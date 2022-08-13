<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Media;

trait BelongsToMedia
{
    /**
     * define the relationship between media and its channel.
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
