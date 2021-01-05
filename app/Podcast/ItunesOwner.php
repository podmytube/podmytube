<?php

namespace App\Podcast;

use InvalidArgumentException;

class ItunesOwner implements IsRenderableInterface
{
    /** @var string $name */
    public $name;

    /** @var string $email */
    public $email;

    private function __construct(array $attributes = [])
    {
        $this->name = $attributes['itunesOwnerName'] ?? null;
        if (isset($attributes['itunesOwnerEmail']) && filter_var($attributes['itunesOwnerEmail'], FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email address is not valid');
        }
        $this->email = $attributes['itunesOwnerEmail'] ?? null;
    }

    public static function prepare(array $attributes = [])
    {
        return new static($attributes);
    }

    public function name()
    {
        return $this->name;
    }

    public function email()
    {
        return $this->email;
    }

    public function render(): string
    {
        if ($this->name || $this->email) {
            return view('podcast.itunesOwner')
                ->with(['itunesOwner' => $this])
                ->render();
        }
        return '';
    }
}
