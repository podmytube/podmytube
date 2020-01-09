<?php

namespace App\Podcast;

use App\Category;

class ItunesCategory implements IsRenderableInterface
{

    protected $name;
    protected $parentName;

    private function __construct(Category $category)
    {
        $this->name = $category->name();
        $this->parentName = $category->parentName();
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
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
        return view('podcast.itunesCategory')->with(["itunesCategory" => $this])->render();
    }
}
