<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Podcast\HeaderImage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HeaderImageTest extends TestCase
{
    public function testingFullInformationsShouldRenderProperly()
    {
        $imageUrl = "https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png";
        $imageLink = "https://www.podmytube.com";
        $imageTitle = "Lorem ipsum";
        $renderedResult = HeaderImage::prepare([
            "url" => $imageUrl,
            "link" => $imageLink,
            "title" => $imageTitle,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>$imageUrl</url>", $renderedResult);
        $this->assertStringContainsString("<link>$imageLink</link>", $renderedResult);
        $this->assertStringContainsString("<title>$imageTitle</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
    }

    public function testingMoreInformationsShouldRenderProperly()
    {
        $imageUrl = "https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png";
        $imageTitle = "Lorem ipsum";
        $renderedResult = HeaderImage::prepare([
            "url" => $imageUrl,
            "title" => $imageTitle,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>$imageUrl</url>", $renderedResult);
        $this->assertStringContainsString("<title>$imageTitle</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
    }

    public function testingPartialInformationsShouldRenderProperly()
    {
        $imageUrl = "https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png";
        $renderedResult = HeaderImage::prepare([
            "url" => $imageUrl,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>$imageUrl</url>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $renderedResult = HeaderImage::prepare()->render();
        $this->assertEmpty($renderedResult);
    }

    public function testingNoInformationShouldRenderEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        HeaderImage::prepare([
            "title" => "Lorem ipsum", 
            "url" => "Invalid email address"
            ]);
    }
}
