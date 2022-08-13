<?php

declare(strict_types=1);

namespace App\Podcast;

use InvalidArgumentException;

class PodcastCover implements IsRenderableInterface
{
    private function __construct(
        public ?string $link = null,
        public ?string $title = null,
        public ?string $url = null
    ) {
        $this->isValidUrl($this->url);
        $this->isValidUrl($this->link);
    }

    public static function prepare(
        ?string $link = null,
        ?string $title = null,
        ?string $url = null
    ) {
        return new static(link: $link, title: $title, url: $url);
    }

    public function isValidUrl(?string $urlToCheck): bool
    {
        if ($urlToCheck === null) {
            return true;
        }

        if (filter_var($urlToCheck, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("Url/link {{$urlToCheck}} is not valid.");
        }

        return true;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function link(): ?string
    {
        return $this->link;
    }

    public function render(): string
    {
        $dataToRender = array_filter(get_object_vars($this), function (
            $property
        ) {
            return isset($property);
        });
        if (!$dataToRender) {
            return '';
        }

        return view('podcast.podcastCover')
            ->with(['podcastCover' => $this])
            ->render()
        ;
    }
}
