<?php

namespace Tests\Traits;

use App\Interfaces\Podcastable;
use App\Podcast\PodcastItem;
use Illuminate\Support\Collection;

trait IsAbleToTestPodcast
{
    public function podcastHeaderInfosChecking(Podcastable $podcastable, array $podcastInfos)
    {
        $expectedKeys = [
            'title',
            'link',
            'description',
            'imageUrl',
            'language',
            'category',
            'explicit',
        ];

        array_map(function ($key) use ($podcastable, $podcastInfos) {
            $this->assertArrayHasKey($key, $podcastInfos, 'Converting a ' . get_class($podcastable) . " to a podcast header should have key {$key}.");
        }, $expectedKeys);
        
        $this->assertEquals($podcastInfos['title'], $podcastable->podcastTitle());
        $this->assertEquals($podcastInfos['link'], $podcastable->podcastLink());
        $this->assertEquals($podcastInfos['description'], $podcastable->podcastDescription());
        $this->assertEquals($podcastInfos['imageUrl'], $podcastable->podcastCoverUrl());
        $this->assertEquals($podcastInfos['language'], $podcastable->podcastLanguage());
        $this->assertEquals($podcastInfos['category'], $podcastable->podcastCategory());
        $this->assertEquals($podcastInfos['copyright'], $podcastable->podcastCopyright());
        $this->assertEquals($podcastInfos['explicit'], $podcastable->podcastExplicit());
    }

    public function podcastItemsChecking(Collection $podcastItems)
    {
        $podcastItems->map(
            function ($podcastItem) {
                $this->assertInstanceOf(
                    PodcastItem::class,
                    $podcastItem,
                    'PodcastItems should be a collection of PodcastItem Object'
                );
            }
        );
    }
}
