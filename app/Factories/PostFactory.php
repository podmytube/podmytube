<?php

namespace App\Factories;

use App\Exceptions\PostCategoryNotWantedHereException;
use App\Post;
use App\PostCategory;
use Carbon\Carbon;

class PostFactory
{
    public const DEFAULT_AUTHOR = 'fred';

    public const DEFAULT_FEATURED_IMAGE = 'https://wpbackend.tyteca.net/wp-content/uploads/2020/09/main-square-500x500-1.jpg';

    /** @var \App\Post $postModel */
    protected $postModel;

    /** @var \App\PostCategory $postCategoryModel */
    protected $postCategoryModel;

    protected $allowedCategories = [
        'podmytube',
    ];

    /** @var array $postData */
    protected $postData;

    private function __construct(array $postData)
    {
        $this->postData = $postData;

        if (!$this->HasItTheGoodCategory()) {
            throw new PostCategoryNotWantedHereException("This post category should not appear on this app.");
        }

        $this->postModel = Post::byWordpressId($this->postId());
        if ($this->postModel === null) {
            $this->postModel = new Post();
            $this->savePostModel();
            return $this;
        }

        if (Carbon::parse($this->postData['modified'])
            ->greaterThan($this->postModel->updated_at)
        ) {
            $this->savePostModel();
        }
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    protected function savePostModel()
    {
        $this->postModel->wp_id = $this->postId();
        $this->postModel->author = $this->postData['_embedded']['author'][0]['name'] ?? self::DEFAULT_AUTHOR;
        $this->postModel->title = $this->postTitle();
        $this->postModel->slug = $this->postSlug();
        $this->postModel->featured_image = $this->postData['_embedded']['wp:featuredmedia'][0]['source_url'] ?? self::DEFAULT_FEATURED_IMAGE;
        $this->postModel->sticky = $this->postData['sticky'];
        $this->postModel->excerpt = $this->postData['excerpt']['rendered'];
        $this->postModel->content = $this->postData['content']['rendered'];
        $this->postModel->format = $this->postData['format'];
        $this->postModel->status = true;
        $this->postModel->published_at = Carbon::parse($this->postData['date'], "Europe/Paris");
        $this->postModel->created_at = Carbon::parse($this->postData['date'], "Europe/Paris");
        $this->postModel->updated_at = $this->postLastUpdate();
        $this->postModel->post_category_id = $this->postCategoryModel->id;
        $this->postModel->save();
    }

    public function post()
    {
        return $this->postModel;
    }

    public function category()
    {
        return $this->postCategoryModel;
    }

    /**
     * extract the category from post.
     * filtering non-category before gettiong the first one.
     */
    public function HasItTheGoodCategory(): bool
    {
        $postCategories = array_values(
            array_filter($this->postData['_embedded']['wp:term'][0], function ($item) {
                if ($item['taxonomy'] === 'category') {
                    return true;
                }
                return false;
            })
        );

        if (!count($postCategories)) {
            return false;
        }

        $firstCategorySlug = $postCategories[0]['slug'];
        if (!in_array($firstCategorySlug, $this->allowedCategories)) {
            return false;
        }

        /** check if category do exist */
        $this->postCategoryModel = PostCategory::bySlug($firstCategorySlug);
        if ($this->postCategoryModel !== null) {
            return true;
        }

        /** if category does not exist => creating category */
        $this->postCategoryModel = PostCategory::create(
            [
                /** extracting first category from json */
                'wp_id' => $postCategories[0]['id'],
                'name' => $postCategories[0]['name'],
                'slug' => $postCategories[0]['slug'],
            ]
        );
        return true;
    }

    protected function postId()
    {
        return $this->postData['id'];
    }

    protected function postTitle()
    {
        return $this->postData['title']['rendered'];
    }

    protected function postSlug()
    {
        return $this->postData['slug'];
    }

    protected function postLastUpdate()
    {
        return Carbon::parse($this->postData['modified'], "Europe/Paris");
    }
}
