<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Thumb;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;

interface Coverable
{
    public function id();

    public function title(): string;

    public function youtubeId(): Attribute;

    public function channelId(): string;

    public function nameWithId(): string;

    public function setCoverFromUploadedFile(UploadedFile $uploadedFile): Thumb;

    public function morphedName(): string;
}
