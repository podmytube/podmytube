<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\NoPostsObtainedException;
use App\Models\Post;
use App\Modules\WordpressPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class WordpressPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_wordpress_factory_url_should_be_good(): void
    {
        $this->assertEquals(
            'https://wpbackend.tyteca.net/wp-json/wp/v2/posts/?_embed&filter[orderby]=modified&page=1',
            WordpressPosts::init()->url()
        );
    }

    public function test_get_json_from_remote_is_working(): void
    {
        $results = WordpressPosts::init()->getPostsFromRemote()->posts();
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
    }

    public function test_update_with_no_data_should_throw_exception(): void
    {
        $this->expectException(NoPostsObtainedException::class);
        WordpressPosts::init()->update();
    }

    public function test_inserting_from_file_should_be_ok_too(): void
    {
        WordpressPosts::init()->getPostsFromFile(__DIR__ . '/../Fixtures/wpbackendSamplePosts.json')->update();
        $this->assertCount(1, Post::all());
    }
}
