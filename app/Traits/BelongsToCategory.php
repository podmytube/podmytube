<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Category;

trait BelongsToCategory
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
