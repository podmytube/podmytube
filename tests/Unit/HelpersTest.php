<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HelpersTest extends TestCase
{
    /** @test */
    public function secondseconds_to_youtube_format_is_working_fine(): void
    {
        $this->assertEquals('PT5S', secondsToYoutubeFormat(5));
        $this->assertEquals('PT6M35S', secondsToYoutubeFormat(395));
        $this->assertEquals('P1DT1H8M52S', secondsToYoutubeFormat(90532));
    }
}
