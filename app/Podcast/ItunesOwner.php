<?php

namespace App\Podcast;

class ItunesOwner implements IsRenderableInterface
{
    protected $name;
    protected $email;

    private function __construct(
        ?string $itunesName = null,
        ?string $itunesEmail = null
    ) {
        $this->name = $itunesName ?? null;

        if (
            $itunesEmail &&
            filter_var($itunesEmail, FILTER_VALIDATE_EMAIL) === false
        ) {
            throw new \InvalidArgumentException('Email address is not valid');
        }
        $this->email = $itunesEmail;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
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
