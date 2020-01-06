<?php

namespace App\Podcast;

class PodcastItems
{

    protected $podcastItems = [];

    private function __construct()
    {
        
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render()
    {
        var_dump()
        return true;
    }
}
