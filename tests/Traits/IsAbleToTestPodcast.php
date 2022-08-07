<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Interfaces\Podcastable;
use App\Models\Category;
use App\Podcast\PodcastItem;
use Illuminate\Support\Collection;

trait IsAbleToTestPodcast
{
    public function podcastHeaderInfosChecking(Podcastable $podcastable, array $podcastInfos): void
    {
        $expectedKeys = [
            'title',
            'link',
            'description',
            'author',
            'email',
            'copyright',
            'imageUrl',
            'language',
            'category',
            'explicit',
        ];

        array_map(function ($key) use ($podcastable, $podcastInfos): void {
            $this->assertArrayHasKey($key, $podcastInfos, 'Converting a ' . get_class($podcastable) . " to a podcast header should have key {$key}.");
        }, $expectedKeys);

        $this->assertEquals($podcastInfos['title'], $podcastable->podcastTitle());
        $this->assertEquals($podcastInfos['link'], $podcastable->podcastLink());
        $this->assertEquals($podcastInfos['description'], $podcastable->podcastDescription());
        $this->assertEquals($podcastInfos['author'], $podcastable->podcastAuthor());
        $this->assertEquals($podcastInfos['email'], $podcastable->podcastEmail());
        $this->assertEquals($podcastInfos['copyright'], $podcastable->podcastCopyright());
        $this->assertEquals($podcastInfos['imageUrl'], $podcastable->podcastCoverUrl());
        $this->assertEquals($podcastInfos['language'], $podcastable->podcastLanguage());
        $this->assertEquals($podcastInfos['category'], $podcastable->podcastCategory());
        $this->assertEquals($podcastInfos['copyright'], $podcastable->podcastCopyright());
        $this->assertEquals($podcastInfos['explicit'], $podcastable->podcastExplicit());
    }

    public function podcastItemsChecking(Collection $podcastItems): void
    {
        $podcastItems->map(
            function ($podcastItem): void {
                $this->assertInstanceOf(
                    PodcastItem::class,
                    $podcastItem,
                    'PodcastItems should be a collection of PodcastItem Object'
                );
            }
        );
    }

    public function itemsChecking(Podcastable $podcastable, string $renderedPodcast): void
    {
        $podcastable->mediasToPublish()->map(function ($media) use ($podcastable, $renderedPodcast): void {
            $this->assertStringContainsString('<guid>' . $media->media_id . '</guid>', $renderedPodcast);
            $this->assertStringContainsString('<title>' . $media->title . '</title>', $renderedPodcast);
            $this->assertStringContainsString(
                '<enclosure url="' . $media->enclosureUrl() . '" length="' . $media->length . '" type="audio/mpeg" />',
                $renderedPodcast
            );
            $this->assertStringContainsString('<pubDate>' . $media->pubDate() . '</pubDate>', $renderedPodcast);
            $this->assertStringContainsString('<itunes:duration>' . $media->duration() . '</itunes:duration>', $renderedPodcast);
            $this->assertStringContainsString(
                '<itunes:explicit>' . $podcastable->podcastExplicit() . '</itunes:explicit>',
                $renderedPodcast
            );
        });
    }

    public function headerChecking($podcastable, $renderedPodcast): void
    {
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $renderedPodcast);
        $this->assertStringContainsString(
            '<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/">',
            $renderedPodcast
        );

        /*
         * =======================================
         * Required tags
         * =======================================
         */

        $this->assertStringContainsString('<title>' . $podcastable->title() . '</title>', $renderedPodcast);
        $this->assertStringContainsString('<description><![CDATA[' . $podcastable->description . ']]></description>', $renderedPodcast);
        $this->assertStringContainsString('<image>', $renderedPodcast);
        $this->assertStringContainsString('<url>' . $podcastable->cover->podcastUrl() . '</url>', $renderedPodcast);
        $this->assertStringContainsString('<title>' . $podcastable->podcastTitle() . '</title>', $renderedPodcast);
        $this->assertStringContainsString('<link>' . $podcastable->podcastLink() . '</link>', $renderedPodcast);
        $this->assertStringContainsString('</image>', $renderedPodcast);
        $this->assertStringContainsString('<language>' . $podcastable->podcastLanguage() . '</language>', $renderedPodcast);
        $this->assertStringContainsString('<itunes:explicit>' . $podcastable->podcastExplicit() . '</itunes:explicit>', $renderedPodcast);
        $this->podcastCategoryChecking($podcastable, $renderedPodcast);

        /*
         * =======================================
         * Optionnal tags
         * =======================================
         */
        if ($podcastable->podcastLink()) {
            $this->assertStringContainsString('<link>' . $podcastable->podcastLink() . '</link>', $renderedPodcast);
        }

        if ($podcastable->podcastCopyright()) {
            $this->assertStringContainsString('<copyright>' . $podcastable->podcastCopyright() . '</copyright>', $renderedPodcast);
        }

        if ($podcastable->podcastAuthor()) {
            $this->assertStringContainsString('<itunes:author>' . $podcastable->podcastAuthor() . '</itunes:author>', $renderedPodcast);
        }

        if ($podcastable->podcastAuthor() || $podcastable->podcastEmail()) {
            $this->assertStringContainsString('<itunes:owner>', $renderedPodcast);
            if ($podcastable->podcastAuthor()) {
                $this->assertStringContainsString('<itunes:name>' . $podcastable->podcastAuthor() . '</itunes:name>', $renderedPodcast);
            }
            if ($podcastable->podcastEmail()) {
                $this->assertStringContainsString('<itunes:email>' . $podcastable->podcastEmail() . '</itunes:email>', $renderedPodcast);
            }
            $this->assertStringContainsString('</itunes:owner>', $renderedPodcast);
        }
        // ...
        $this->assertStringContainsString('</rss>', $renderedPodcast);
    }

    public function podcastCategoryChecking(Podcastable $podcastable, $renderedPodcast): void
    {
        $this->assertInstanceOf(Category::class, $podcastable->podcastCategory());
        if ($podcastable->podcastCategory()->parent) {
            $this->assertStringContainsString('<itunes:category text="' . htmlentities($podcastable->podcastCategory()->parent->name) . '">', $renderedPodcast);
            $this->assertStringContainsString('<itunes:category text="' . htmlentities($podcastable->podcastCategory()->name) . '" />', $renderedPodcast);
            $this->assertStringContainsString('</itunes:category>', $renderedPodcast);
        } else {
            $this->assertStringContainsString('<itunes:category text="' . htmlentities($podcastable->podcastCategory()->name) . '" />', $renderedPodcast);
        }
    }
}
