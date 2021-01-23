<?php

namespace App\Traits;

use App\Category;

trait BelongsToCategory
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
