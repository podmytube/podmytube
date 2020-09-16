<?php

namespace Tests\Unit;

use App\Factories\WordpressBackendFactory;
use App\Modules\WordpressPosts;
use Tests\TestCase;

class WordpressBackendFactoryTest extends TestCase
{
    public function testWordpressFactoryUrlShouldBeGood()
    {
        $this->assertEquals(
            'http://blog.local.tyteca.net/wp-json/wp/v2/posts/?_embed&filter[orderby]=modified&page=1',
            WordpressBackendFactory::create()->url()
        );
    }

    public function testGetJson()
    {
        WordpressPosts::fromWordpressBackend(
            WordpressBackendFactory::create()->url()
        );
    }
}
