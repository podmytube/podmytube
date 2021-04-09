<?php

namespace App\Podcast;

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
        if (isset($attributes['url'])) {
            $this->setUrl($attributes['url']);
        }
        if (isset($attributes['link'])) {
            $this->setLink($attributes['link']);
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function setUrl(?string $url = null)
    {
        if ($this->isValidUrl($url)) {
            $this->url = $url;
        }
    }

    public function setLink(?string $link = null)
    {
        if ($this->isValidUrl($link)) {
            $this->link = $link;
        }
    }

    public function isValidUrl(string $urlToCheck)
    {
        if (filter_var($urlToCheck, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(
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
            if (isset($property)) {
                return true;
            }
            return false;
        });
        if (!$dataToRender) {
            return '';
        }
        return view('podcast.podcastCover')
            ->with(['podcastCover' => $this])
            ->render();
    }
}
