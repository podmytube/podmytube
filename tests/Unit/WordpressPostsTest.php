<?php

namespace Tests\Unit;

use App\Exceptions\NoPostsObtainedException;
use App\Modules\WordpressPosts;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordpressPostsTest extends TestCase
{
    use RefreshDatabase;

    public function testWordpressFactoryUrlShouldBeGood()
    {
        $this->assertEquals(
            'https://wpbackend.tyteca.net/wp-json/wp/v2/posts/?_embed&filter[orderby]=modified&page=1',
            WordpressPosts::init()->url()
        );
    }

    public function testGetJsonFromRemoteIsWorking()
    {
        $results = WordpressPosts::init()->getPostsFromRemote()->posts();
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
    }

    public function testUpdateWithNoDataShouldThrowException()
    {
        $this->expectException(NoPostsObtainedException::class);
        WordpressPosts::init()->update();
    }

    public function testInsertingFromRemoteShouldBeOk()
    {
        $expectedResult = [
            'wp_id' => 12,
            'author' => 'fred',
            'title' => 'featured image post',
            'slug' => 'testing',
            'featured_image' => 'https://wpbackend.tyteca.net/wp-content/uploads/2020/09/main-square-500x500-1.jpg',
            'excerpt' => '<p>This post is only a test.</p>' . PHP_EOL,
            'format' => 'standard',
            'status' => 1,
            'published_at' => '2020-09-17 18:30:49',
            'post_category_id' => '1',
        ];

        WordpressPosts::init()->getPostsFromRemote()->update();

        /** this post is uncategorized */
        $this->assertNull(Post::byWordpressId(18));

        $this->assertGreaterThanOrEqual(1, Post::count());

        /** checking the first post if available */
        $insertedPost = Post::byWordpressId($expectedResult['wp_id']);
        array_walk($expectedResult, function ($value, $key) use ($insertedPost) {
            $this->assertEquals($value, $insertedPost->$key, "$key should be $value");
        });
        //$this->assertFalse($insertedPost->sticky, "Sticky should be false");
        $this->assertStringContainsString('<p class="has-text-align-center">This post is only a test.</p>', $insertedPost->content);
    }
}
