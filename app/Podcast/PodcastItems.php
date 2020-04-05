<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\PodcastHasNoMediaToPublish;
use App\Modules\EnclosureUrl;

class PodcastItems
{
    protected $medias;
    protected $channel;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
        $this->collectItemsToPublish();
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    /**
     * This function will return all medias to be published.
     *
     * @throw PodcastHasNoMediaToPublish when no medias has been grabbed
     */
    protected function collectItemsToPublish()
    {
        $this->medias = $this->channel
            ->medias()
            ->orderBy('published_at', 'desc')
            ->get()
            /** removing item not grabbed */
            ->filter(function ($element) {
                return !empty($element->grabbed_at);
            });
    }

    /**
     * this function will render items in podcast feed.
     *
     * @return string xml data for feed items.
     */
    public function render()
    {
        return view('podcast.items')
            ->with(['medias' => $this->medias])
            ->render();
    }
}
