<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Thumb;
use Illuminate\Database\Seeder;

class ThumbsPolymorphismSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** transforming all existing thumbs into coverables */
        $existingThumbs = Thumb::all();
        if ($existingThumbs->count()) {
            $existingThumbs->each(
                function (Thumb $thumb): void {
                    $status = $thumb->update(
                        [
                            'coverable_type' => 'App\Models\Channel',
                            'coverable_id' => $thumb->channel_id,
                        ]
                    );
                }
            );
        }
    }
}
