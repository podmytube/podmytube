<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Thumb;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;

interface Coverable
{
    // internal db id
    public function id();

    public function title(): string;

    // return true youtube id (Channel_id != Playlist_id)
    public function youtubeId(): Attribute;

    // returns the channel youtube id of the channel
    public function channelId(): string;

    public function nameWithId(): string;

    public function setCoverFromUploadedFile(UploadedFile $uploadedFile): Thumb;

    public function morphedName(): string;
}
