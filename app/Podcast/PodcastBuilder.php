<?php

namespace App\Podcast;

class PodcastBuilder
{
    protected $header;
    protected $items;

    private function __construct(
        PodcastHeader $podcastHeader,
        PodcastItems $podcastItems
    ) {
        $this->header = $podcastHeader;
        $this->items = $podcastItems;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render(string $destinationFile)
    {
        if (!is_writable(pathinfo($destinationFile, PATHINFO_DIRNAME))) {
            throw new \InvalidArgumentException("Destination file {{$destinationFile}} is not writable.");
        }
        $this->header->render();
        $this->items->render();
        var_dump("finishing rendering");
        return true;
    }
}
