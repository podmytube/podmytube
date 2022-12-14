<?php

declare(strict_types=1);

namespace App\Modules;

use App\Exceptions\NoPostsObtainedException;
use App\Exceptions\PostCategoryNotWantedHereException;
use App\Factories\PostFactory;
use Illuminate\Support\Facades\Log;

/**
 * This class is updating posts from worpress api.
 * It is :
 * - querying one wordpress api
 * - parsing the results
 * - calling PostFactory that is creating the Post and the PostCategory (if needed).
 */
class WordpressPosts
{
    /** @var string wordpress api endpoint */
    protected $endpoint = 'posts';

    /** @var int we only need page 1 - i won't publish posts this fast enough */
    protected $page = 1;

    /** @var array json decoded posts */
    protected $posts = [];

    /** @var int */
    protected $importedPosts = 0;

    private function __construct()
    {
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function baseUrl()
    {
        return config('app.wpbackend') . '/wp-json/wp/v2/';
    }

    public function url()
    {
        return $this->baseUrl() . $this->endpoint . '/?_embed&filter[orderby]=modified&page=' . $this->page;
    }

    /**
     * will obtain posts from remote.
     */
    public function getPostsFromRemote(): self
    {
        $response = file_get_contents($this->url(), false);
        $this->posts = json_decode($response, true);

        return $this;
    }

    /**
     * will obtain posts from one file (mainly for tests).
     */
    public function getPostsFromFile(string $filename): self
    {
        $response = file_get_contents($filename, false);
        $this->posts = json_decode($response, true);

        return $this;
    }

    public function posts(): array
    {
        return $this->posts;
    }

    public function importedPosts()
    {
        return $this->importedPosts;
    }

    public function update(): self
    {
        if (!count($this->posts())) {
            throw new NoPostsObtainedException(
                'No post has been obtained yet. You should use getPostsFromRemote/getPostsFromFile before.'
            );
        }

        array_map(
            function ($postData): void {
                try {
                    PostFactory::create($postData);
                    ++$this->importedPosts;
                } catch (PostCategoryNotWantedHereException $exception) {
                    Log::error("Post {{$postData['title']['rendered']}} does not belong here");
                }
            },
            $this->posts()
        );

        return $this;
    }
}
