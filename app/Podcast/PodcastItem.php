<?php

namespace App\Podcast;

class PodcastItem
{
    protected $guid;
    protected $title;
    protected $link;
    protected $description;
    protected $duration;
    protected $enclosure;
    protected $pubDate;

    protected $itunesItem;

    private function __construct(array $attributes)
    {
        $this->guid = $attributes['guid'] ?? null;
        $this->title = $attributes['title'] ?? null;
        $this->link = $attributes['link'] ?? null;
        $this->description = $attributes["description"] ?? null;
        $this->duration = $attributes["duration"] ?? null;
        $this->enclosure = $attributes["enclosure"] ?? null;
        $this->pubDate = $attributes["pubDate"] ?? null;
        $this->itunesItem = $attributes['itunesItem'] ?? null;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render()
    {
        var_dump("rendering item");
        return true;
    }
}
