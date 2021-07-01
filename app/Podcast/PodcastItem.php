<?php

declare(strict_types=1);

namespace App\Podcast;

use App\Exceptions\PodcastItemNotValidException;

class PodcastItem
{
    /** @var string */
    public $guid;
    /** @var string */
    public $title;
    /** @var string */
    public $enclosureUrl;
    /** @var string */
    public $mediaLength;
    /** @var string */
    public $pubDate;
    /** @var string */
    public $description;
    /** @var string */
    public $duration;
    /** @var string */
    public $explicit;

    private function __construct(array $itemData)
    {
        array_map(function ($property) use ($itemData): void {
            $this->{$property} = $itemData[$property];
        }, array_keys(get_object_vars($this)));

        $this->check();
    }

    public static function with(array $itemData)
    {
        return new static($itemData);
    }

    public function check()
    {
        array_map(function ($requiredField): void {
            if ($this->{$requiredField} === null || strlen($this->{$requiredField}) <= 0) {
                throw new PodcastItemNotValidException("{$requiredField} is required for one podcast item to be valid.");
            }
        }, ['title', 'enclosureUrl']);

        if ($this->mediaLength <= 0 || $this->mediaLength === null) {
            throw new PodcastItemNotValidException('Podcastitem mediaLength must be set and greater than 0.');
        }

        return true;
    }

    /**
     * this function will render items in podcast feed.
     *
     * @return string xml data for feed items
     */
    public function render()
    {
        return view('podcast.item')
            ->with(['item' => $this])
            ->render()
        ;
    }
}
