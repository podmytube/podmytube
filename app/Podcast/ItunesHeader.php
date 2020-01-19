<?php

namespace App\Podcast;

use App\Podcast\ItunesOwner;
use App\Podcast\ItunesCategory;

class ItunesHeader implements IsRenderableInterface
{
    const _TYPE_EPISODIC = 'episodic';
    const _TYPE_SERIAL = 'serial';

    /** @var string title */
    protected $title;

    /** @var string author */
    protected $author;

    /** @var string type (episodic or serial) */
    protected $type;

    /** @var string imageUrl */
    protected $imageUrl;

    /** @var boolean explicit */
    protected $explicit;

    /** @var ItunesCategory category object*/
    protected $itunesCategory;

    /** @var ItunesOwner itunesOwner object */
    protected $itunesOwner;

    private function __construct(array $attributes = [])
    {
        $this->title = $attributes["title"] ?? null;
        $this->author = $attributes["author"] ?? null;
        $this->explicit = $attributes["explicit"] ?? null;

        if (isset($attributes["imageUrl"])) {
            $this->setImageUrl($attributes["imageUrl"]);
        }

        if (isset($attributes["type"])) {
            $this->setType($attributes["type"]);
        }

        if (isset($attributes["itunesCategory"]) && $attributes["itunesCategory"] instanceof ItunesCategory) {
            $this->itunesCategory = $attributes["itunesCategory"];
        }

        if (isset($attributes["itunesOwner"]) && $attributes["itunesOwner"] instanceof ItunesOwner) {
            $this->itunesOwner = $attributes["itunesOwner"];
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render(): string
    {
        $dataToRender = array_filter(get_object_vars($this), function ($property) {
            if (isset($property)) {
                return true;
            }
            return false;
        });
        if (!$dataToRender) {
            return "";
        }
        return view('podcast.itunesHeader')->with(["itunesHeader" => $this])->render();
    }

    public function title()
    {
        return $this->title;
    }

    public function author()
    {
        return $this->author;
    }

    public function type()
    {
        return $this->type;
    }

    public function explicit()
    {
        return $this->explicit ? 'true' : 'false';
    }

    public function itunesOwner()
    {
        return $this->itunesOwner;
    }

    public function itunesCategory()
    {
        return $this->itunesCategory;
    }

    public function imageUrl()
    {
        return $this->imageUrl;
    }

    protected function setType(string $type = self::_TYPE_EPISODIC)
    {
        if ($type == self::_TYPE_EPISODIC || $type == self::_TYPE_SERIAL) {
            $this->type = $type;
        }
    }

    protected function setImageUrl(string $imageUrl)
    {
        if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException("ImageUrl is not a valid url.");
        }
        $this->imageUrl = $imageUrl;
    }
}
