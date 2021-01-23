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

        $this->assertEquals($podcastInfos['title'], $podcastable->title());
        $this->assertEquals($podcastInfos['link'], $podcastable->link());
        $this->assertEquals($podcastInfos['description'], $podcastable->description());
        $this->assertEquals($podcastInfos['imageUrl'], $podcastable->podcastCoverUrl());
        $this->assertEquals($podcastInfos['language'], $podcastable->languageCode());
        $this->assertEquals($podcastInfos['category'], $podcastable->category());
        $this->assertEquals($podcastInfos['copyright'], $podcastable->copyright());
        $this->assertEquals($podcastInfos['explicit'], $podcastable->explicit());
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
