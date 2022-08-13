<?php

declare(strict_types=1);

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
    $this->post = Post::factory()->create();
});

it('should return post url with sitemapLoc', function (): void {
    $expectedSitemapLoc = route($this->post->sitemapRouteName, $this->post);
    $this->assertEquals($expectedSitemapLoc, $this->post->sitemapLoc());
});

it('should return updated_at with sitemapLastmod', function (): void {
    $result = $this->post->sitemapLastmod();
    expect($result)->not()->toBeNull();
    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->toDateTimeString())->toBe($this->post->updated_at->toDateTimeString());
});

it('should return "daily" with sitemapChangeFreq', function (): void {
    $this->assertEquals('daily', $this->post->sitemapChangeFreq());
});

it('should return "1.0" with sitemapPriority', function (): void {
    $this->assertEquals(1.0, $this->post->sitemapPriority());
});
