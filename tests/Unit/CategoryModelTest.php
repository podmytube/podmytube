<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_simple_feed_value_is_correct(): void
    {
        $categoryName = 'Fashion & Beauty';
        $category = Category::factory()->create(['name' => $categoryName]);
        $this->assertEquals(
            htmlentities($categoryName),
            $category->feedValue()
        );
    }

    public function test_parent_feed_value_is_correct(): void
    {
        $parentCategoryName = 'Kids & Family';
        $childCategoryName = 'Pets & Animals';
        $parentCategory = Category::factory()->create(['name' => $parentCategoryName]);
        $category = Category::factory()->create(
            [
                'parent_id' => $parentCategory->id,
                'name' => $childCategoryName, ]
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
    public function by_slug_is_null_when_invalid_slug(): void
    {
        $this->assertNull(Category::bySlug('not-valid-category-slug'));
    }

    /** @test */
    public function by_slug_is_ok_with_non_parent_category(): void
    {
        Category::factory()->create(['slug' => 'find-me']);
        $result = Category::bySlug('find-me');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Category::class, $result);
    }

    /** @test */
    public function by_slug_is_ok_with_parent_category(): void
    {
        $category = Category::factory()->create(['slug' => 'find-me']);
        // creating sub category
        Category::factory()->create(['parent_id' => $category->id, 'slug' => 'sub-cat']);
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
