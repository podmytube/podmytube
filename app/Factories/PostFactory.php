<?php

namespace App\Factories;

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


    /** @var array $postData */
    protected $postData;

    private function __construct(array $postData)
    {
        $this->postData = $postData;

        $this->postCategoryModel = $this->extractCategory();
        if ($this->postCategoryModel === null) {
            /** @todo */
        }

        $this->postModel = Post::create([
            'wp_id' => $this->postData['id'],
            'author' => $this->postData['_embedded']['author'][0]['name'] ?? self::DEFAULT_AUTHOR,
            'title' => $this->postData['title']['rendered'],
            'slug' => $this->postData['slug'],
            'featured_image' => $this->postData['_embedded']['wp:featuredmedia'][0]['source_url'] ?? self::DEFAULT_FEATURED_IMAGE,
            'sticky' => $this->postData['sticky'],
            'excerpt' => $this->postData['excerpt']['rendered'],
            'content' => $this->postData['content']['rendered'],
            'format' => $this->postData['format'],
            'status' => true,
            'published_at' => Carbon::parse($this->postData['date'], "Europe/Paris"),
            'created_at' => Carbon::parse($this->postData['date'], "Europe/Paris"),
            'updated_at' => Carbon::parse($this->postData['modified'], "Europe/Paris"),
            'post_category_id' => $this->postCategoryModel->id,
        ]);
    }

    public static function create(...$params)
    {
        return new static(...$params);
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
    public function extractCategory(): PostCategory
    {
        $availableCategories = array_values(
            array_filter($this->postData['_embedded']['wp:term'][0], function ($item) {
                if ($item['taxonomy'] === 'category') {
                    return true;
                }
                return false;
            })
        );

        if (!count($availableCategories)) {
            return null;
        }

        /** check if category do exist */
        $postCategoryModel = PostCategory::byWordpressId($availableCategories[0]['id']);
        if ($postCategoryModel !== null) {
            return $postCategoryModel;
        }

        /** if category does not exist => creating category */
        return PostCategory::create(
            [
                /** extracting first category from json */
                'wp_id' => $availableCategories[0]['id'],
                'name' => $availableCategories[0]['name'],
                'slug' => $availableCategories[0]['slug'],
            ]
        );
    }
}
