<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Podcast\PodcastCover;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PodcastCoverTest extends TestCase
{
    /** @test */
    public function full_informations_should_render_properly(): void
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $imageLink = 'https://www.podmytube.com';
        $imageTitle = 'Lorem ipsum';
        $renderedResult = PodcastCover::prepare(
            url: $imageUrl,
            link: $imageLink,
            title: $imageTitle,
        )->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString("<link>{$imageLink}</link>", $renderedResult);
        $this->assertStringContainsString("<title>{$imageTitle}</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
        $this->assertStringContainsString('<itunes:image href="' . $imageUrl . '" />', $renderedResult);
    }

    /** @test */
    public function more_informations_should_render_properly(): void
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $imageTitle = 'Lorem ipsum';
        $renderedResult = PodcastCover::prepare(url: $imageUrl, title: $imageTitle)->render();
        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString("<title>{$imageTitle}</title>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
        $this->assertStringContainsString('<itunes:image href="' . $imageUrl . '" />', $renderedResult);

        $this->assertStringNotContainsString('<link>', $renderedResult);
    }

    /** @test */
    public function partial_informations_should_render_properly(): void
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $renderedResult = PodcastCover::prepare(url: $imageUrl)->render();

        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString("<url>{$imageUrl}</url>", $renderedResult);
        $this->assertStringContainsString('</image>', $renderedResult);
        $this->assertStringContainsString('<itunes:image href="' . $imageUrl . '" />', $renderedResult);

        $this->assertStringNotContainsString('<link>', $renderedResult);
        $this->assertStringNotContainsString('<title>', $renderedResult);
    }

    /** @test */
    public function no_informations_should_render_nothing(): void
    {
        $renderedResult = PodcastCover::prepare()->render();
        $this->assertEmpty($renderedResult);
    }

    /** @test */
    public function invalid_url_should_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PodcastCover::prepare(title: 'Lorem ipsum', url: 'Invalid url.');
    }

    /** @test */
    public function invalid_link_should_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PodcastCover::prepare(title: 'Lorem ipsum', link: 'Invalid link.');
    }
}
