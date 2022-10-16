<?php

declare(strict_types=1);

namespace App\Traits;

use App\Interfaces\Coverable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

trait HasVignette
{
    protected string $vignetteDisk = 'vignettes';

    public function hasVignette(): bool
    {
        return $this->vignetteFileExists();
    }

    /**
     * used as a property $this->vignette_url.
     */
    public function vignetteUrl(): Attribute
    {
        throw_unless(
            $this instanceof Coverable,
            new InvalidArgumentException("Object is not implementing Coverable. It's impossible for it to get a vignette.")
        );

        return Attribute::get(function () {
            if (!$this->hasCover()) {
                return defaultVignetteUrl();
            }

            return Storage::disk($this->vignetteDisk)->url($this->vignetteRelativePath());
        });
    }

    public function vignetteFileExists(): bool
    {
        if ($this->cover === null) {
            return false;
        }

        return Storage::disk('vignettes')->fileExists($this->vignetteRelativePath());
    }

    public function vignetteRelativePath(): string
    {
        throw_unless(
            $this instanceof Coverable,
            new InvalidArgumentException("Object is not implementing Coverable. It's impossible for it to get a vignette.")
        );

        // building vignette path from channel id and thumb/cover filename
        return $this->channelId() . '/' .
            pathinfo($this->cover->file_name, PATHINFO_FILENAME) . '_vig.'
            . pathinfo($this->cover->file_name, PATHINFO_EXTENSION);
    }
}
