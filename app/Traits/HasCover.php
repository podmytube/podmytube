<?php

namespace App\Traits;

use App\Thumb;
use Illuminate\Http\UploadedFile;

trait HasCover
{
    public function cover()
    {
        return $this->morphOne(Thumb::class, 'coverable');
    }

    public function setCover(UploadedFile $uploadedFile)
    {
        return Thumb::updateOrCreate(
            [
                'coverable_type' => get_class($this),
                'coverable_type' => $this->id(),
            ],
            [
                'file_size' => $uploadedFile->getSize(),
                /** get filename of the stored file */
                'file_name' => basename($uploadedFile->store($this->channelId(), Thumb::LOCAL_STORAGE_DISK)),
                'file_disk' => Thumb::LOCAL_STORAGE_DISK,
            ]
        );
    }
}
