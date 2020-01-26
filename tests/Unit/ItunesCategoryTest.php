<?php

namespace Tests\Unit;

use App\Category;
use Tests\TestCase;
use App\Podcast\ItunesCategory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItunesCategoryTest extends TestCase
{
    public function testingTranslationWithAmpersand()
    {
        $categoryObj=ItunesCategory::prepare(Category::find(89));
        $this->assertEquals("Society &amp; Culture", $categoryObj->parentName());
        $this->assertEquals("Places &amp; Travel", $categoryObj->name());
    }

    public function testingWithAmpersandCategory()
    {
        $renderedText = ItunesCategory::prepare(Category::find(86))->render();
        $this->assertStringContainsString('<itunes:category text="Society &amp; Culture">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Documentary" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testingWithParentCategory()
    {
        $renderedText = ItunesCategory::prepare(Category::find(94))->render();
        $this->assertStringContainsString('<itunes:category text="Sports">', $renderedText);
        $this->assertStringContainsString('<itunes:category text="Fantasy Sports" />', $renderedText);
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testingSimpleCategory()
    {
        $renderedText = ItunesCategory::prepare(Category::find(10))->render();
        $this->assertStringContainsString('<itunes:category text="Leisure" />', $renderedText);
    }

    public function testingNoCategorySet()
    {
        $renderedText = ItunesCategory::prepare()->render();
        $this->assertEmpty($renderedText);
    }
}
