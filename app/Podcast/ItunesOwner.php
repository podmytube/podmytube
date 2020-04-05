<?php

namespace App\Podcast;

class ItunesOwner implements IsRenderableInterface
{
    public $name;
    public $email;

    private function __construct(
        string $itunesName = null,
        string $itunesEmail = null
    ) {
        $this->setItunesName($itunesName);
        if (isset($itunesEmail)) {
            $this->setItunesEmail($itunesEmail);
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function setItunesName(string $itunesName = null)
    {
        $this->name = $itunesName ?? null;
    }

    public function setItunesEmail(string $itunesEmail)
    {
        if (filter_var($itunesEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Email address is not valid');
        }

        $this->email = $itunesEmail;
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
