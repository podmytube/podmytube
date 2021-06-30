<?php

declare(strict_types=1);

namespace App\Traits;

use App\Thumb;
use Illuminate\Http\UploadedFile;

trait HasCover
{
    public function cover()
    {
        return $this->morphOne(Thumb::class, 'coverable');
    }

    public function hasCover(): bool
    {
        return $this->cover !== null;
    }

    public function setCoverFromUploadedFile(UploadedFile $uploadedFile): Thumb
    {
        return Thumb::updateOrCreate(
            [
                'coverable_type' => get_class($this),
                'coverable_id' => $this->id(),
            ],
            [
                'file_size' => $uploadedFile->getSize(),
                // get filename of the stored file
                'file_name' => basename($uploadedFile->store($this->channelId(), Thumb::LOCAL_STORAGE_DISK)),
                'file_disk' => Thumb::LOCAL_STORAGE_DISK,
            ]
        );
    }

    public function setCoverFromThumb(Thumb $thumb): bool
    {
        return $thumb->update([
            'coverable_type' => get_class($this),
            'coverable_id' => $this->id(),
        ]);
    }
}
