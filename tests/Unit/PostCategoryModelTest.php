<?php

namespace Tests\Unit;

use App\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function testByWordpressIdShouldReturnNull()
    {
        $this->assertNull(PostCategory::byWordpressId(-12));
    }

    public function testByWordpressIdIsWorkingFine()
    {
        $expectedPostCategory = factory(PostCategory::class)->create();
        $postModel = PostCategory::byWordpressId($expectedPostCategory->wp_id);
        $this->assertEquals($expectedPostCategory->id, $postModel->id);
        $this->assertInstanceOf(PostCategory::class, $postModel);
    }

    public function testBySlugShouldReturnNull()
    {
        $this->assertNull(PostCategory::bySlug('db-is-empty'));
    }

    public function testBySlugIsWorkingFine()
    {
        $expectedPostCategory = factory(PostCategory::class)->create();
        $postModel = PostCategory::bySlug($expectedPostCategory->slug);
        $this->assertEquals($expectedPostCategory->id, $postModel->id);
        $this->assertInstanceOf(PostCategory::class, $postModel);
    }
}
