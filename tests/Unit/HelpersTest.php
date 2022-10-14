<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HelpersTest extends TestCase
{
    /** @test */
    public function seconds_to_youtube_format_is_working_fine(): void
    {
        $this->assertEquals('PT5S', secondsToYoutubeFormat(5));
        $this->assertEquals('PT6M35S', secondsToYoutubeFormat(395));
        $this->assertEquals('P1DT1H8M52S', secondsToYoutubeFormat(90532));
    }

    /** @test */
    public function youtube_channel_url_should_be_fine(): void
    {
        $this->assertEquals('https://www.youtube.com/channel/foo', youtubeChannelUrl('foo'));
        $this->assertEquals('https://www.youtube.com/channel/UCu0tUATmSnMMCbCRRYXmVlQ', youtubeChannelUrl('UCu0tUATmSnMMCbCRRYXmVlQ'));
    }

    /** @test */
    public function fixtures_path_should_be_fine(): void
    {
        $this->assertEquals('/app/tests/Fixtures/foo', fixtures_path('foo'));
        $this->assertEquals('/app/tests/Fixtures/lorem/ipsum/dolore', fixtures_path('lorem/ipsum/dolore'));
        $this->assertEquals('/app/tests/Fixtures/lorem/ipsum/dolore', fixtures_path('/lorem/ipsum/dolore'));
    }

    /** @test */
    public function default_vignette_url_should_be_fine(): void
    {
        $this->assertEquals(config('app.thumbs_url') . '/default_vignette.jpg', defaultVignetteUrl());
    }

     /** @test */
     public function default_cover_url_should_be_fine(): void
     {
         $this->assertEquals(config('app.thumbs_url') . '/default_thumb.jpg', defaultCoverUrl());
     }
}
