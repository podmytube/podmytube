<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasFile
{
    protected $fileDisk;
    protected $relativePath;

    public function defineFileRequirements(
        string $fileDisk,
        string $relativePath
    ): self {
        $this->fileDisk = $fileDisk;
        $this->relativePath = $relativePath;
        return $this;
    }

    /**
     * Check if file exists
     *
     * @return bool true if file
     */
    public function exists(): bool
    {
        return Storage::disk($this->fileDisk)->exists($this->relativePath());
    }
}
