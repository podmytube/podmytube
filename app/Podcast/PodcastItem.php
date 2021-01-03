<?php

namespace App\Podcast;

use App\Exceptions\PodcastItemNotValidException;

class PodcastItem
{
    /** @var string $guid */
    public $guid;
    /** @var string $title */
    public $title;
    /** @var string $enclosureUrl */
    public $enclosureUrl;
    /** @var string $mediaLength */
    public $mediaLength;
    /** @var string $pubDate */
    public $pubDate;
    /** @var string $description */
    public $description;
    /** @var string $duration */
    public $duration;
    /** @var string $explicit */
    public $explicit;

    private function __construct(array $itemData)
    {
        array_map(function ($property) use ($itemData) {
            $this->$property = $itemData[$property];
        }, array_keys(get_object_vars($this)));
        if (!$this->isValid()) {
            $message = 'Podcast item is not valid.';
            $message .= "title received : ({$itemData['title']})";
            $message .= "enclosureUrl received ({$itemData['enclosureUrl']}). ";
            $message .= "mediaLength received ({$itemData['mediaLength']}). ";
            throw new PodcastItemNotValidException();
        }
    }

    public static function with(array $itemData)
    {
        return new static($itemData);
    }

    public function isValid()
    {
        foreach (['title', 'enclosureUrl'] as $requiredField) {
            if ($this->$requiredField === null || strlen($this->$requiredField) <= 0) {
                return false;
            }
        }
        if ($this->mediaLength === null || $this->mediaLength <= 0) {
            return false;
        }
        return true;
    }

    /**
     * this function will render items in podcast feed.
     *
     * @return string xml data for feed items.
     */
    public function render()
    {
        return view('podcast.item')
            ->with(['item' => $this])
            ->render();
    }
}
