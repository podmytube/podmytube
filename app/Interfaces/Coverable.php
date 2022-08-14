<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Thumb;
use Illuminate\Http\UploadedFile;

interface Coverable
{
    public function id();

    public function title(): string;

    public function youtubeId(): string;

    public function channelId(): string;

    public function nameWithId(): string;

    public function setCoverFromUploadedFile(UploadedFile $uploadedFile): Thumb;

    public function morphedName(): string;
}
