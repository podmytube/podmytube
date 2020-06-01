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
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
    }

    public function testDocumentaryWillDisplaySocietyAndCulture()
    {
        $renderedText = ItunesCategory::prepare(
            Category::where('name', '=', 'documentary')->first()
        )->render();
        $this->assertStringContainsString(
            '<itunes:category text="Society &amp; Culture">',
            $renderedText
        );
        $this->assertStringContainsString(
            '<itunes:category text="Documentary" />',
            $renderedText
        );
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testFantasySportsWillDisplaySports()
    {
        $renderedText = ItunesCategory::prepare(
            Category::where('name', '=', 'fantasySports')->first()
        )->render();
        $this->assertStringContainsString(
            '<itunes:category text="Sports">',
            $renderedText
        );
        $this->assertStringContainsString(
            '<itunes:category text="Fantasy Sports" />',
            $renderedText
        );
        $this->assertStringContainsString('</itunes:category>', $renderedText);
    }

    public function testingSimpleCategory()
    {
        $renderedText = ItunesCategory::prepare(
            Category::where('name', '=', 'leisure')->first()
        )->render();
        $this->assertStringContainsString(
            '<itunes:category text="Leisure" />',
            $renderedText
        );
    }

    public function testingNoCategorySet()
    {
        $renderedText = ItunesCategory::prepare()->render();
        $this->assertEmpty($renderedText);
    }
}