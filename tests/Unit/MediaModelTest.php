<?php

namespace Tests\Unit;

use App\Media;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaModelTest extends TestCase
{
    use RefreshDatabase;

    public function testingFactory()
    {
        $medias = factory(Media::class,3)->make([
            'month' => 8,
            'year' => 2018,

        ]);
        dump($medias);
    }
}
