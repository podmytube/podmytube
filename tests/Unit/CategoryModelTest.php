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

    /** @test */
    public function by_slug_is_null_when_invalid_slug()
    {
        $this->assertNull(Category::bySlug('not-valid-category-slug'));
    }

    /** @test */
    public function by_slug_is_ok_with_non_parent_category()
    {
        factory(Category::class)->create(['slug' => 'find-me']);
        $result = Category::bySlug('find-me');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Category::class, $result);
    }

    /** @test */
    public function by_slug_is_ok_with_parent_category()
    {
        $category = factory(Category::class)->create(['slug' => 'find-me']);
        /** creating sub category */
        factory(Category::class)->create(['parent_id' => $category->id, 'slug' => 'sub-cat']);
        $result = Category::bySlug('find-me');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('find-me', $result->slug);

        $result = Category::bySlug('sub-cat');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('sub-cat', $result->slug);
    }
}
