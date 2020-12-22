<?php

namespace Tests\Unit;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSimpleFeedValueIsCorrect()
    {
        $categoryName = 'Fashion & Beauty';
        $category = factory(Category::class)->create(['name' => $categoryName]);
        $this->assertEquals(
            htmlentities($categoryName),
            $category->feedValue()
        );
    }

    public function testParentFeedValueIsCorrect()
    {
        $parentCategoryName = 'Kids & Family';
        $childCategoryName = 'Pets & Animals';
        $parentCategory = factory(Category::class)->create(['name' => $parentCategoryName]);
        $category = factory(Category::class)->create(
            [
                'parent_id' => $parentCategory->id,
                'name' => $childCategoryName]
        );
        $this->assertEquals(
            htmlentities($parentCategoryName),
            $category->parentFeedValue()
        );
        $this->assertEquals(
            htmlentities($childCategoryName),
            $category->feedValue()
        );
    }
}
