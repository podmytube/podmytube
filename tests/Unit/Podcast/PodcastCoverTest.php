<?php

namespace Tests\Unit\Podcast;

use Tests\TestCase;
use App\Podcast\PodcastCover;

class PodcastCoverTest extends TestCase
{
    public function testingFullInformationsShouldRenderProperly()
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $imageLink = 'https://www.podmytube.com';
        $imageTitle = 'Lorem ipsum';
        $renderedResult = PodcastCover::prepare([
            'url' => $imageUrl,
            'link' => $imageLink,
            'title' => $imageTitle,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString("<link>{$imageLink}</link>", $renderedResult);
        $this->assertStringContainsString("<title>{$imageTitle}</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
    }

    public function testingMoreInformationsShouldRenderProperly()
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $imageTitle = 'Lorem ipsum';
        $renderedResult = PodcastCover::prepare([
            'url' => $imageUrl,
            'title' => $imageTitle,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString("<title>{$imageTitle}</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);

        $this->assertStringNotContainsString('<link>', $renderedResult);
    }

    public function testingPartialInformationsShouldRenderProperly()
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $renderedResult = PodcastCover::prepare([
            'url' => $imageUrl,
        ])->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);

        $this->assertStringNotContainsString('<link>', $renderedResult);
        $this->assertStringNotContainsString('<title>', $renderedResult);
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $renderedResult = PodcastCover::prepare()->render();
        $this->assertEmpty($renderedResult);
    }

    public function testingInvalidUrlShouldThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);
        PodcastCover::prepare([
            'title' => 'Lorem ipsum',
            'url' => 'Invalid url.'
        ]);
    }
}
