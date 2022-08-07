<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PostCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_wordpress_id_should_return_null(): void
    {
        $this->assertNull(PostCategory::byWordpressId(-12));
    }

    public function test_by_wordpress_id_is_working_fine(): void
    {
        $expectedPostCategory = PostCategory::factory()->create();
        $postModel = PostCategory::byWordpressId($expectedPostCategory->wp_id);
        $this->assertEquals($expectedPostCategory->id, $postModel->id);
        $this->assertInstanceOf(PostCategory::class, $postModel);
    }

    public function test_by_slug_should_return_null(): void
    {
        $this->assertNull(PostCategory::bySlug('db-is-empty'));
    }

    public function test_by_slug_is_working_fine(): void
    {
        $expectedPostCategory = PostCategory::factory()->create();
        $postModel = PostCategory::bySlug($expectedPostCategory->slug);
        $this->assertEquals($expectedPostCategory->id, $postModel->id);
        $this->assertInstanceOf(PostCategory::class, $postModel);
    }
}
