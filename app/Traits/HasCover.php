<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Thumb;
use Illuminate\Http\UploadedFile;
use ReflectionClass;

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
                'coverable_type' => $this->morphedName(),
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
            'coverable_type' => $this->morphedName(),
            'coverable_id' => $this->id(),
        ]);
    }

    public function morphedName(): string
    {
        $reflect = new ReflectionClass(static::class);

        return 'morphed' . $reflect->getShortName();
    }

    public function coverFolderPath(): string
    {
        return config('app.thumbs_path') . '/' . $this->relativeFolderPath();
    }
}
