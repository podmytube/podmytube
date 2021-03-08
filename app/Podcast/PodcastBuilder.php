<?php

namespace App\Podcast;

class PodcastBuilder
{
    /** @var string $podcastHeader */
    public $podcastHeader;

    /** @var string $podcastItems */
    public $podcastItems;

    private function __construct(array $attributes = [])
    {
        $this->podcastItems = $attributes['podcastItems'] ?? null;
        $this->podcastHeader = PodcastHeader::create($attributes)->render();
        $this->podcastItems = PodcastItems::with($this->podcastItems)->render();
    }

    public static function create(array $attributes = [])
    {
        return new static($attributes);
    }

    public function render(): string
    {
        return view('podcast.main')
            ->with(['podcast' => $this])
            ->render();
    }
}
