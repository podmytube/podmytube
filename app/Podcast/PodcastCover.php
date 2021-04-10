<?php

namespace App\Podcast;

use InvalidArgumentException;

class PodcastCover implements IsRenderableInterface
{
    /** @var string url*/
    public $url;

    /** @var string link*/
    public $link;

    /** @var string title*/
    public $title;

    private function __construct(array $attributes = [])
    {
        $this->title = $attributes['title'] ?? null;
        $this->url = $attributes['url'] ?? null;
        $this->link = $attributes['link'] ?? null;

        $this->isValidUrl($this->url);
        $this->isValidUrl($this->link);
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function isValidUrl(?string $urlToCheck)
    {
        if ($urlToCheck === null) {
            return true;
        }
        if (filter_var($urlToCheck, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException(
                "Url/link {{$urlToCheck}} is not valid."
            );
        }
        return true;
    }

    public function url()
    {
        return $this->url;
    }

    public function title()
    {
        return $this->title;
    }

    public function link()
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
            ->render();
    }
}
