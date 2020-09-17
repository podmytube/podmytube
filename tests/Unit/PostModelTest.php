<?php

namespace Tests\Unit;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function testByWordpressIdShouldReturnNull()
    {
        $this->assertNull(Post::byWordpressId(-12));
    }

    public function testByWordpressIdIsWorkingFine()
    {
        $expectedPost = factory(Post::class)->create();
        $postModel = Post::byWordpressId($expectedPost->wp_id);
        $this->assertEquals($expectedPost->id, $postModel->id);
        $this->assertInstanceOf(Post::class, $postModel);
    }
}
