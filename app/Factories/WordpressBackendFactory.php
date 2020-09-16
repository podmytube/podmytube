<?php

namespace App\Factories;

class WordpressBackendFactory
{
    /** @var integer $page current page wanted */
    protected $page = 1;

    /** @var string $endpoint current endpoint */
    protected $endpoint = 'posts';

    private function __construct()
    {
        //code
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function url()
    {
        return config('app.wpbackend') . '/wp-json/wp/v2/' . $this->endpoint . '/?_embed&filter[orderby]=modified&page=' . $this->page;
    }
}
