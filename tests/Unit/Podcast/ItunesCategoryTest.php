<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Category;
use App\Podcast\ItunesCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ItunesCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedCategories();
    }

    /** @test */
    public function documentary_will_display_society_and_culture(): void
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('documentary'))->render();
        $this->assertStringContainsString('<itunes:category text="Society &amp; Culture">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Documentary" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    /** @test */
    public function fantasy_sports_will_display_sports(): void
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('fantasy-sports'))->render();
        $this->assertStringContainsString('<itunes:category text="Sports">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Fantasy Sports" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    /** @test */
    public function simple_category(): void
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('leisure'))->render();
        $this->assertStringContainsString('<itunes:category text="Leisure" />', $renderedText);
    }

    /** @test */
    public function no_category_set(): void
    {
        $renderedText = ItunesCategory::prepare(null)->render();
        $this->assertEmpty($renderedText);
    }
}
