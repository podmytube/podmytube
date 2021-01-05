<?php

namespace App\Podcast;

use App\Category;
use InvalidArgumentException;

class ItunesHeader implements IsRenderableInterface
{
    public const TYPE_EPISODIC = 'episodic';
    public const TYPE_SERIAL = 'serial';

    /** @var string $title */
    public $title;
    /** @var string $author */
    public $author;
    /** @var string $email */
    public $email;
    /** @var bool $explicit */
    public $explicit = false;
    /** @var string $type (episodic or serial) */
    public $type;
    /** @var string $imageUrl */
    public $imageUrl;
    /** @var string $itunesCategory*/
    public $itunesCategory;
    /** @var ItunesOwner itunesOwner object */
    public $itunesOwner;

    private function __construct(array $attributes = [])
    {
        $this->title = $attributes['title'] ?? null;
        $this->author = $attributes['author'] ?? null;
        $this->email = $attributes['email'] ?? null;
        $this->explicit = $attributes['explicit'] ?? null;

        if (isset($attributes['imageUrl'])) {
            $this->setImageUrl($attributes['imageUrl']);
        }

        if (isset($attributes['type'])) {
            $this->setType($attributes['type']);
        }

        if (isset($attributes['itunesCategory']) && $attributes['itunesCategory'] instanceof Category) {
            $this->itunesCategory = ItunesCategory::prepare($attributes['itunesCategory'])->render();
        }

        if ($this->author || $this->email) {
            $this->itunesOwner = ItunesOwner::prepare(['itunesOwnerName' => $this->author, 'itunesOwnerEmail' => $this->email, ])
                ->render();
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render(): string
    {
        if (array_filter(get_object_vars($this), function ($property) {
            if (isset($property)) {
                return true;
            }
            return false;
        }) === false) {
            return '';
        }
        return view('podcast.itunesHeader')
            ->with(['itunesHeader' => $this])
            ->render();
    }

    public function explicit()
    {
        return $this->explicit ? 'true' : 'false';
    }

    protected function setType(string $type = self::TYPE_EPISODIC)
    {
        if ($type === self::TYPE_EPISODIC || $type === self::TYPE_SERIAL) {
            $this->type = $type;
        }
    }

    protected function setImageUrl(string $imageUrl)
    {
        if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('ImageUrl is not a valid url.');
        }
        $this->imageUrl = $imageUrl;
    }
}
