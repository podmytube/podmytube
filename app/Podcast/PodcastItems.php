<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\PodcastHasNoMediaToPublish;
use App\Modules\EnclosureUrl;

class PodcastItems
{
    protected $items;
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
        $this->items = $this->channel
            ->medias()
            ->orderBy('published_at', 'desc')
            ->get()
            /** removing item not grabbed */
            ->filter(function ($element) {
                return (!empty($element->grabbed_at));
            })
            
            /** adding pubdate and enclosure part */
            
            ;
        if ($this->items->isEmpty()) {
            throw new PodcastHasNoMediaToPublish("This channel {{$this->channel->channel_id}} has no items to publish.");
        }
        
        $this->items->map(function ($item) {
            $item->pubDate = $item->published_at->timezone('Europe/Paris')->format(DATE_RSS);
            $item->enclosure = array(
                'length' => $item->length,
                'url' => EnclosureUrl::create($item)->get(),
            );
        });
    }

    public function render()
    {
        var_dump($this->items);
        return view('podcast.items')->with(["items" => $this->items])->render();
    }
}
