<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_wordpress_id_should_return_null(): void
    {
        $this->assertNull(Post::byWordpressId(-12));
    }

    public function test_by_wordpress_id_is_working_fine(): void
    {
        $expectedPost = Post::factory()->create();
        $postModel = Post::byWordpressId($expectedPost->wp_id);
        $this->assertEquals($expectedPost->id, $postModel->id);
        $this->assertInstanceOf(Post::class, $postModel);
    }
}
