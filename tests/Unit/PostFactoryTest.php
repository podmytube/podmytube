<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\PostCategoryNotWantedHereException;
use App\Factories\PostFactory;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PostFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_minimal_checking(): void
    {
        $postFactory = PostFactory::create(
            json_decode(file_get_contents(__DIR__ . '/../Fixtures/wpbackendSinglePost.json'), true)
        );

        $post = $postFactory->post();

        // basic elements
        $this->assertEquals(12, $post->wp_id);
        $this->assertEquals('fred', $post->author);
        $this->assertEquals('featured image post', $post->title);
        $this->assertEquals('featured-image-post', $post->slug);
        $this->assertFalse($post->sticky);
        $this->assertStringContainsString('This post is only a test.', $post->excerpt);
        $this->assertStringContainsString('class="has-text-align-center">This post is only a test.</p>', $post->content);
        $this->assertEquals('standard', $post->format);
        $this->assertTrue($post->status);

        // featured image
        $this->assertEquals(
            'https://wpbackend.tyteca.net/wp-content/uploads/2020/09/main-square-500x500-1.jpg',
            $post->featured_image
        );

        // dates
        $this->assertInstanceOf(Carbon::class, $post->published_at);
        $this->assertEquals('2020-09-17 18:30:49', $post->published_at->format('Y-m-d H:i:s'));

        $this->assertInstanceOf(Carbon::class, $post->created_at);
        $this->assertEquals('2020-09-17 18:30:49', $post->created_at->format('Y-m-d H:i:s'));

        $this->assertInstanceOf(Carbon::class, $post->updated_at);
        $this->assertEquals('2020-09-17 22:08:48', $post->updated_at->format('Y-m-d H:i:s'));

        /** category part */
        $category = $postFactory->category();
        $this->assertEquals(12, $category->wp_id);
        $this->assertEquals('Podmytube.com', $category->name);
        $this->assertEquals('podmytube', $category->slug);
    }

    public function test_category_not_allowed_should_be_rejected(): void
    {
        $this->expectException(PostCategoryNotWantedHereException::class);
        PostFactory::create(
            json_decode(file_get_contents(__DIR__ . '/../Fixtures/wpbackendRejectedPost.json'), true)
        );
    }

    public function test_post_is_only_inserted_once(): void
    {
        PostFactory::create(json_decode(file_get_contents(__DIR__ . '/../Fixtures/wpbackendSinglePost.json'), true));
        PostFactory::create(json_decode(file_get_contents(__DIR__ . '/../Fixtures/wpbackendSinglePostEdited.json'), true));
        $this->assertEquals(1, Post::count());
        $this->assertEquals('featured image post modified', Post::first()->title);
        $this->assertEquals('featured-image-post', Post::first()->slug);
    }
}
