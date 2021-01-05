<?php

namespace App\Podcast;

class PodcastHeader
{
    /** @var string $url */
    public $link;

    /** @var string title */
    public $title;

    /** @var string $language */
    public $language;

    /** @var string $copyright */
    public $copyright;

    /** @var string $description the show description */
    public $description;

    /** @var string $podcastCover */
    public $podcastCover;

    /** @var string $itunesHeader */
    public $itunesHeader;

    private function __construct(array $attributes = [])
    {
        $this->link = $attributes['link'] ?? null;
        $this->title = $attributes['title'] ?? null;
        $this->language = $attributes['language'] ?? null;
        $this->copyright = $attributes['copyright'] ?? null;
        $this->description = $attributes['description'] ?? null;

        $this->podcastCover = PodcastCover::prepare([
            'url' => $attributes['imageUrl'] ?? null,
            'link' => $attributes['link'] ?? null,
            'title' => $attributes['title'] ?? null,
        ])->render();

        $this->itunesHeader = ItunesHeader::prepare([
            'author' => $attributes['author'] ?? null,
            'email' => $attributes['email'] ?? null,
            'title' => $attributes['title'] ?? null,
            'imageUrl' => $attributes['podcastCoverUrl'] ?? null,
            'itunesCategory' => $attributes['category'] ?? null,
            'explicit' => $attributes['explicit'] ?? null,
        ])->render();
    }

    public static function create(array $attributes = [])
    {
        return new static($attributes);
    }

    public function render()
    {
        if (array_filter(get_object_vars($this), function (
            $property
        ) {
            if (isset($property)) {
                return true;
            }
            return false;
        }) === false) {
            return '';
        }
        return view('podcast.header')
            ->with(['podcastHeader' => $this])
            ->render();
    }
}
