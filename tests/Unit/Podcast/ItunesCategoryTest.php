<?php

namespace Tests\Unit\Podcast;

use App\Category;
use App\Podcast\ItunesCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ItunesCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedCategories();
    }

    public function testDocumentaryWillDisplaySocietyAndCulture()
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('documentary'))->render();
        $this->assertStringContainsString('<itunes:category text="Society &amp; Culture">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Documentary" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testFantasySportsWillDisplaySports()
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('fantasy-sports'))->render();
        $this->assertStringContainsString('<itunes:category text="Sports">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Fantasy Sports" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testingSimpleCategory()
    {
        $renderedText = ItunesCategory::prepare(Category::bySlug('leisure'))->render();
        $this->assertStringContainsString('<itunes:category text="Leisure" />', $renderedText);
    }

    public function testingNoCategorySet()
    {
        $renderedText = ItunesCategory::prepare(null)->render();
        $this->assertEmpty($renderedText);
    }
}
