<?php

declare(strict_types=1);

use App\Post;
use App\Sitemap\Sitemap;
use App\Sitemap\SitemapNode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
    $this->sitemapHeader = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
EOT;
    $this->sitemapFooter = '</urlset>';
});

it('should return basic sitemap', function (): void {
    $result = Sitemap::init()->render();

    expect($result)->toBe($this->sitemapHeader . PHP_EOL . $this->sitemapFooter . PHP_EOL);
});

it('should include website node', function (): void {
    $sitemapNode = SitemapNode::withRoute(loc: route('www.index'));

    $expectedNode = expectedSitemapNode(loc: config('app.website_url'));

    $result = Sitemap::init()->addNode($sitemapNode)->render();

    expect($result)->toBe($this->sitemapHeader . PHP_EOL . $expectedNode . PHP_EOL . $this->sitemapFooter . PHP_EOL);
});

it('should include post node', function (): void {
    $post = Post::factory()->create();
    $sitemapNode = SitemapNode::withSitemapable($post);

    $expectedNode = expectedSitemapNode(loc: $post->sitemapLoc(), lastmod: $post->sitemapLastmod(), priority: '1.0');

    $result = Sitemap::init()->addNode($sitemapNode)->render();

    expect($result)->toBe($this->sitemapHeader . PHP_EOL . $expectedNode . PHP_EOL . $this->sitemapFooter . PHP_EOL);
});

it('should include both post node and website', function (): void {
    $post = Post::factory()->create();
    $sitemapPostNode = SitemapNode::withSitemapable($post);
    $sitemapWebsiteNode = SitemapNode::withRoute(loc: route('www.index'));

    $result = Sitemap::init()->addNodes(collect([$sitemapWebsiteNode, $sitemapPostNode]))->render();

    $expectedNodeWww = expectedSitemapNode(loc: config('app.website_url'));
    $expectedNodePost = expectedSitemapNode(loc: $post->sitemapLoc(), lastmod: $post->sitemapLastmod(), priority: '1.0');

    expect($result)->toBe($this->sitemapHeader . PHP_EOL . $expectedNodeWww . PHP_EOL . $expectedNodePost . PHP_EOL . $this->sitemapFooter . PHP_EOL);
});

it('should save sitemap', function (): void {
    $post = Post::factory()->create();
    $sitemapPostNode = SitemapNode::withSitemapable($post);
    $sitemapWebsiteNode = SitemapNode::withRoute(loc: route('www.index'));

    Storage::fake('public');

    $sitemap = Sitemap::init()->addNodes(collect([$sitemapWebsiteNode, $sitemapPostNode]));
    $result = $sitemap->render();
    $sitemap->save();

    expect(public_path('sitemap.xml'))->toBeFile();
    expect(file_get_contents(public_path('sitemap.xml')))->toBe($result);
});
