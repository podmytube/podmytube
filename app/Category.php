<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Category extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
    ];

    public $timestamps = false;

    /**
     * One category by ask for its parent category.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship between one category and its children.
     * One category may have many subcategories.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * This function will return category (with children) that has the name received.
     *
     * @param String $categoryName category name
     *
     * @return Collection|null the result
     */
    public static function byName(string $categoryName): ?Category
    {
        return self::where('name', $categoryName)
            ->with('children')
            ->first();
    }

    public static function bySlug(string $slug): ?Category
    {
        return self::where('slug', $slug)->first();
    }

    /**
     * This function will return the whole list of categories (with children).
     *
     * @return Collection|null the whole list
     */
    public static function list(): ?Collection
    {
        return self::where('parent_id', 0)
            ->with('children')
            ->get();
    }

    public function name()
    {
        return $this->name;
    }

    public function parentName()
    {
        if (!$this->parent) {
            return null;
        }
        return $this->parent->name;
    }

    public function feedValue()
    {
        return htmlentities($this->name());
    }

    public function parentFeedValue()
    {
        if ($this->parentName()) {
            return htmlentities($this->parentName());
        }
        return null;
    }
}
