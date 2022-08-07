<?php

declare(strict_types=1);

namespace App\Modules;

use App\Models\Media;

class EnclosureUrl
{
    protected $media;
    protected $mp3BaseUrl;
    protected $enclosureUrl;

    private function __construct(Media $media)
    {
        $this->media = $media;
        $this->build();
    }

    public static function create(...$params): EnclosureUrl
    {
        return new static(...$params);
    }

    public function get(): string
    {
        return $this->enclosureUrl;
    }

    protected function build(): void
    {
        $this->enclosureUrl = config('app.mp3_url') . '/' . $this->media->channel_id . '/' . $this->media->media_id . '.mp3';
    }
}
