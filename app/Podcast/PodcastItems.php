<?php

namespace App\Podcast;

use Illuminate\Support\Collection;

class PodcastItems
{
    /** @var \Illuminate\Support\Collection $podcastItems */
    protected $podcastItems;

    private function __construct(?Collection $podcastItems)
    {
        $this->podcastItems = $podcastItems;
    }

    public static function with(?Collection $podcastItems)
    {
        return new static($podcastItems);
    }

    /**
     * this function will render items in podcast feed.
     *
     * @return string xml data for feed items.
     */
    public function render()
    {
        $items = '';
        if ($this->podcastItems === null || $this->podcastItems->count() <= 0) {
            return $items;
        }

        foreach ($this->podcastItems as $podcastItem) {
            $items .= $podcastItem->render() . "\n";
        }

        return view('podcast.items')
            ->with(['items' => $items])
            ->render();
    }
}
