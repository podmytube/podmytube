<?php

namespace App\Traits;

use App\Category;

trait HasOneCategory
{
    public function category()
    {
        return $this->hasOne(Category::class);
    }
}
