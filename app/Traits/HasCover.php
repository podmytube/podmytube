<?php

declare(strict_types=1);

namespace App\Traits;

use App\Interfaces\Coverable;
use App\Models\Thumb;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use ReflectionClass;

trait HasCover
{
    protected string $coverDisk = 'thumbs';

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

    public function attachCover(Thumb $thumb)
    {
        return $this->cover()->save($thumb);
    }

    public function morphedName(): string
    {
        $reflect = new ReflectionClass(static::class);

        return 'morphed' . $reflect->getShortName();
    }

    public function coverFullPath(): string
    {
        return config('app.thumbs_path') . '/' . $this->coverRelativePath();
    }

    public function coverFolderPath(): string
    {
        return config('app.thumbs_path') . '/' . $this->channelId();
    }

    /**
     * used as a property $this->vignette_url.
     */
    public function coverUrl(): Attribute
    {
        throw_unless(
            $this instanceof Coverable,
            new InvalidArgumentException("Object is not implementing Coverable. It's impossible for it to get a cover.")
        );

        return Attribute::get(function () {
            if (!$this->hasCover()) {
                return defaultCoverUrl();
            }

            return Storage::disk($this->coverDisk)->url($this->coverRelativePath());
        });
    }

    public function coverRelativePath(): string
    {
        throw_unless(
            $this instanceof Coverable,
            new InvalidArgumentException("Object is not implementing Coverable. It's impossible for it to get a vignette.")
        );

        // building cover path from channel id and thumb/cover filename
        return $this->channelId() . '/' . $this->cover->file_name;
    }

    public function coverFileExists(): bool
    {
        if ($this->cover === null) {
            return false;
        }

        return Storage::disk($this->coverDisk)->exists($this->coverRelativePath());
    }
}
