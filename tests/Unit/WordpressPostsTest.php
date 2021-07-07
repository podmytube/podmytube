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

    public function testInsertingFromFileShouldBeOkToo()
    {
        WordpressPosts::init()->getPostsFromFile(__DIR__ . '/../Fixtures/wpbackendSamplePosts.json')->update();
        $this->assertCount(1, Post::all());
    }
}
