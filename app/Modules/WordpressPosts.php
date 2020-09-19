<?php

namespace App\Modules;

use App\Exceptions\NoPostsObtainedException;
use App\Post;
use App\PostCategory;
use Carbon\Carbon;

class WordpressPosts
{
    protected $endpoint = 'posts';
    protected $page = 1;

    /** @var array json decoded posts */
    protected $posts = [];

    protected $allowedCategoriesId = [
        2, // podmytube
    ];

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
     * 
     * @param string $filename
     */
    public function getPostsFromFile(string $filename): self
    {
        $response = file_get_contents($filename, false);
        $this->posts = json_decode($response);
        return $this;
    }

    public function posts(): array
    {
        return $this->posts;
    }

    public function update(): self
    {
        if (!count($this->posts())) {
            throw new NoPostsObtainedException("No post has been obtained yet. You should use getPostsFromRemote/getPostsFromFile before.");
        }

        array_map(
            function ($postData) {

                dump($postData);
                /** importing only the categories I need */
                if (!in_array($this->getPostCategoryId($postData), $this->allowedCategoriesId)) {
                    return false;
                }

                $postModel = Post::byWordpressId($postData->id);
                if ($postModel === null) {
                    /** if post does not exist we create it */
                    return $this->createPost($postData);
                }

                /** if it has been modified on wpbackend, update the local copy */
                /* if ($postModel->updated_at < $this->carbonDate($postData->modified)) {
                    return $this->updatePost($postModel, $postData);
                } */
            },
            $this->posts
        );
        return $this;
    }

    protected function getPostCategoryId($postData)
    {
        return collect($postData->_embedded->{"wp:term"})->collapse()->where('taxonomy', 'category')->first()->id;
    }

    protected function carbonDate($date)
    {
        return Carbon::parse($date, "Europe/Paris");
    }

    protected function createPost($data): \App\Post
    {
        return Post::create(
            [
                'wp_id' => $data->id,
                'author' => "fred", //$this->getAuthor($data->_embedded->author),
                'title' => $data->title->rendered,
                'slug' => $data->slug,
                'featured_image' => $this->featuredImage($data->_embedded),
                'sticky' => $data->sticky ?? false,
                'excerpt' => $data->excerpt->rendered,
                'content' => $data->content->rendered,
                'format' => $data->format,
                'status' => true,
                'published_at' => $this->carbonDate($data->date),
                'created_at' => $this->carbonDate($data->date),
                'updated_at' => $this->carbonDate($data->modified),
                'post_category_id' => $this->getCategory($data->_embedded->{"wp:term"})->id,
            ]
        );
    }

    public function featuredImage($data)
    {
        if (property_exists($data, "wp:featuredmedia")) {
            $data = head($data->{"wp:featuredmedia"});
            if (isset($data->source_url)) {
                return $data->source_url;
            }
        }
        return null;
    }

    public function getCategory($data)
    {
        /** extracting first category from json */
        $category = collect($data)->collapse()->where('taxonomy', 'category')->first();

        /** check if we have this one */
        $postCategoryModel = PostCategory::byWordpressId($category->id);
        if ($postCategoryModel === null) {
            /** if not creating category */
            return $this->createPostCategory($category);
        }
    }

    protected function createPostCategory($data)
    {
        return PostCategory::create(
            [
                'wp_id' => $data->id,
                'name' => $data->name,
                'slug' => $data->slug,
            ]
        );
    }
}
