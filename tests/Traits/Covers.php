<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Interfaces\Coverable;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Thumb;
use Illuminate\Support\Facades\Storage;

trait Covers
{
    public function createCoverFor(Coverable|Channel|Playlist $coverable): Thumb
    {
        $thumb = Thumb::factory()->create([
            'coverable_type' => $coverable->morphedName(),
            'coverable_id' => $coverable->id(),
        ]);
        $this->createFakeCoverFor($thumb);

        return $thumb;
    }

    /** will create a cover from existing fixture and return filesize */
    public function createFakeCoverFor(Thumb $thumb): int
    {
        /** create channel folder */
        $fileName = $thumb->file_name;
        $filePath = $thumb->coverable->channelId() . '/' . $fileName;
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)
            ->put(
                $filePath,
                file_get_contents(fixtures_path('/images/sampleThumb.jpg'))
            )
        ;

        return Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filePath);
    }

    public function vignetteFilePath(Coverable $coverable)
    {
        return $coverable->channelId() . '/' .
            pathinfo($coverable->cover->file_name, PATHINFO_FILENAME) .
            '_vig.' .
            pathinfo($coverable->cover->file_name, PATHINFO_EXTENSION);
    }

    public function coverFilePath(Coverable $coverable)
    {
        return $coverable->channelId() . '/' . $coverable->cover->file_name;
    }
}
