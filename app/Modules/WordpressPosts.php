<?php

namespace App\Modules;

use App\Post;
use Carbon\Carbon;

class WordpressPosts
{
    private function __construct(string $url)
    {
        $this->url = $url;
        $this->importPosts();
    }

    public static function fromWordpressBackend(...$params)
    {
        return new static(...$params);
    }

    public function importPosts($page = 1)
    {
        collect($this->getJson($this->url))
            ->map(function ($post) {
                $this->syncPost($post);
            });
    }

    protected function getJson($url)
    {
        $response = file_get_contents($url, false);
        dd(json_decode($response));
        return json_decode($response);
    }

    protected function syncPost($data)
    {
        $found = Post::where('wp_id', $data->id)->first();

        if (!$found) {
            return $this->createPost($data);
        }

        if ($found and $found->updated_at->format("Y-m-d H:i:s") < $this->carbonDate($data->modified)->format("Y-m-d H:i:s")) {
            return $this->updatePost($found, $data);
        }
    }

    protected function carbonDate($date)
    {
        return Carbon::parse($date);
    }

    protected function createPost($data)
    {
        $post = new Post();
        $post->id = $data->id;
        $post->wp_id = $data->id;
        $post->user_id = $this->getAuthor($data->_embedded->author);
        $post->title = $data->title->rendered;
        $post->slug = $data->slug;
        $post->featured_image = $this->featuredImage($data->_embedded);
        $post->featured = ($data->sticky) ? 1 : null;
        $post->excerpt = $data->excerpt->rendered;
        $post->content = $data->content->rendered;
        $post->format = $data->format;
        $post->status = 'publish';
        $post->publishes_at = $this->carbonDate($data->date);
        $post->created_at = $this->carbonDate($data->date);
        $post->updated_at = $this->carbonDate($data->modified);
        $post->category_id = $this->getCategory($data->_embedded->{"wp:term"});
        $post->save();
        $this->syncTags($post, $data->_embedded->{"wp:term"});
        return $post;
    }
}
