<?php

namespace Database\Seeders;

use App\Thumb;
use Illuminate\Database\Seeder;

class ThumbsPolymorphismSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** transforming all existing thumbs into coverables */
        $existingThumbs = Thumb::all();
        if ($existingThumbs->count()) {
            $existingThumbs->map(
                function (Thumb $thumb) {
                    $status = $thumb->update(
                        [
                            'coverable_type' => 'App\Channel',
                            'coverable_id' => $thumb->channel_id,
                        ]
                    );
                }
            );
        }
    }
}
