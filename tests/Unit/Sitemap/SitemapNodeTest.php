<?php

declare(strict_types=1);

use App\Models\Post;
use App\Sitemap\SitemapNode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

it('should return classic xml node', function (): void {
    $result = SitemapNode::withRoute(loc: route('www.index'))->render();

    expect($result)->toBe(expectedSitemapNode(loc: config('app.website_url')));
});

it('should return xml node with specific changefreq', function (): void {
    $result = SitemapNode::withRoute(loc: route('www.index'), changefreq: 'weekly')->render();

    expect($result)->toBe(expectedSitemapNode(loc: config('app.website_url'), changefreq: 'weekly'));
});

it('should return xml node with specific priority', function (): void {
    $result = SitemapNode::withRoute(loc: route('www.index'), priority: '0.7')->render();

    expect($result)->toBe(expectedSitemapNode(loc: config('app.website_url'), changefreq: 'daily', priority: '0.7'));
});

it('should return xml node with sitemapable', function (): void {
    $post = Post::factory()->create();
    $result = SitemapNode::withSitemapable($post)->render();

    expect($result)->toBe(expectedSitemapNode(loc: $post->sitemapLoc(), lastmod: $post->sitemapLastmod(), changefreq: 'daily', priority: '1.0'));
});
