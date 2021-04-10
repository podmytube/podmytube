<?php

namespace App\Podcast;

use App\Category;

class ItunesCategory implements IsRenderableInterface
{
    protected $name;
    protected $parentName;

    private function __construct(?Category $category = null)
    {
        if ($category !== null) {
            $this->name = $category->feedValue();
            $this->parentName = $category->parentFeedValue();
        }
    }

    public static function prepare(?Category $category)
    {
        return new static($category);
    }

    public function name()
    {
        return $this->name;
    }

    public function parentName()
    {
        return $this->parentName;
    }

    public function render(): string
    {
        if (!$this->name) {
            return '';
        }
        return view('podcast.itunesCategory')
            ->with(['itunesCategory' => $this])
            ->render();
    }
}
