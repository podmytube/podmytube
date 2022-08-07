<?php

declare(strict_types=1);

namespace App\Podcast;

use InvalidArgumentException;

class ItunesHeader implements IsRenderableInterface
{
    public const TYPE_EPISODIC = 'episodic';
    public const TYPE_SERIAL = 'serial';

    /** @var string */
    public $title;

    /** @var string */
    public $author;

    /** @var string */
    public $email;

    /** @var ?\App\Models\Category */
    public $category;

    /** @var string */
    public $explicit = 'false';

    /** @var string (episodic or serial) */
    public $type;

    /** @var string */
    public $imageUrl;

    /** @var string */
    public $itunesCategory;

    /** @var ItunesOwner itunesOwner object */
    public $itunesOwner;

    private function __construct(array $attributes = [])
    {
        $this->title = $attributes['title'] ?? null;
        $this->author = $attributes['author'] ?? null;
        $this->email = $attributes['email'] ?? null;
        $this->category = $attributes['category'] ?? null;

        if (isset($attributes['explicit'])) {
            $this->explicit = self::checkExplicit($attributes['explicit']);
        }

        if (isset($attributes['imageUrl'])) {
            $this->setImageUrl($attributes['imageUrl']);
        }

        if (isset($attributes['type'])) {
            $this->setType($attributes['type']);
        }

        $this->itunesCategory = ItunesCategory::prepare($this->category)->render();

        $this->itunesOwner = ItunesOwner::prepare(['itunesOwnerName' => $this->author, 'itunesOwnerEmail' => $this->email])
            ->render()
        ;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render(): string
    {
        if (array_filter(get_object_vars($this), function ($property) {
            return isset($property);
        }) === false) {
            return '';
        }

        return view('podcast.itunesHeader')
            ->with(['itunesHeader' => $this])
            ->render()
        ;
    }

    public static function checkExplicit($explicit): string
    {
        if (is_bool($explicit)) {
            return $explicit === true ? 'true' : 'false';
        }
        if ($explicit === 'true') {
            return 'true';
        }

        return 'false';
    }

    protected function setType(string $type = self::TYPE_EPISODIC): void
    {
        if ($type === self::TYPE_EPISODIC || $type === self::TYPE_SERIAL) {
            $this->type = $type;
        }
    }

    protected function setImageUrl(string $imageUrl): void
    {
        if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('ImageUrl is not a valid url.');
        }
        $this->imageUrl = $imageUrl;
    }
}
