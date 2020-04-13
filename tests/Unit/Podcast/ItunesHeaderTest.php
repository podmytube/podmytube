<?php

namespace Tests\Unit;

use App\Category;
use App\Podcast\ItunesCategory;
use App\Podcast\ItunesHeader;
use App\Podcast\ItunesOwner;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ItunesHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
    }

    public function testingWithMoreInformations()
    {
        $authorName = 'Gérard Choufleur';
        $authorEmail = 'gchoufleur@gmail.com';
        $itunesTitle = 'Mangeur de chamois';
        $renderedResult = ItunesHeader::prepare([
            'author' => $authorName,
            'title' => $itunesTitle,
            'imageUrl' => Thumb::defaultUrl(),
            'itunesOwner' => ItunesOwner::prepare($authorName, $authorEmail),
            'itunesCategory' => ItunesCategory::prepare(
                Category::where('name', '=', 'documentary')->first()
            ),
            'explicit' => true,
        ])->render();
        $this->assertStringContainsString(
            "<itunes:author>$authorName</itunes:author>",
            $renderedResult
        );
        $this->assertStringContainsString(
            "<itunes:title>$itunesTitle</itunes:title>",
            $renderedResult
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            "<itunes:name>$authorName</itunes:name>",
            $renderedResult
        );
        $this->assertStringContainsString(
            "<itunes:email>$authorEmail</itunes:email>",
            $renderedResult
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:explicit>true</itunes:explicit>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:category text="Society &amp; Culture">',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:category text="Documentary" />',
            $renderedResult
        );
        $this->assertStringContainsString(
            '</itunes:category>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:image href="' . Thumb::defaultUrl() . '" />',
            $renderedResult
        );
    }

    public function testingWithOwner()
    {
        $authorName = 'Gérard Choufleur';
        $renderedResult = ItunesHeader::prepare([
            'author' => $authorName,
            'itunesOwner' => ItunesOwner::prepare($authorName),
        ])->render();
        $this->assertStringContainsString(
            "<itunes:author>$authorName</itunes:author>",
            $renderedResult
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            "<itunes:name>$authorName</itunes:name>",
            $renderedResult
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:explicit>false</itunes:explicit>',
            $renderedResult
        );
    }

    public function testingAuthor()
    {
        $authorName = 'Gérard Choufleur';
        $renderedResult = ItunesHeader::prepare([
            'author' => $authorName,
        ])->render();
        $this->assertStringContainsString(
            "<itunes:author>$authorName</itunes:author>",
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:explicit>false</itunes:explicit>',
            $renderedResult
        );
    }

    public function testingImageUrl()
    {
        $imageUrl =
            'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $renderedResult = ItunesHeader::prepare([
            'imageUrl' => $imageUrl,
        ])->render();
        $this->assertStringContainsString(
            "<itunes:image href=\"$imageUrl\" />",
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:explicit>false</itunes:explicit>',
            $renderedResult
        );
    }

    public function testingInvalidImageUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        ItunesHeader::prepare([
            'imageUrl' => 'this is not a valid url',
        ])->render();
    }

    public function testingInvalidType()
    {
        $renderedResult = ItunesHeader::prepare([
            'type' => 'InvalidType',
        ])->render();
        $this->assertStringNotContainsString('<itunes:type>', $renderedResult);
    }

    public function testingEpisodicType()
    {
        $renderedResult = ItunesHeader::prepare([
            'type' => ItunesHeader::TYPE_EPISODIC,
        ])->render();
        $this->assertStringContainsString(
            '<itunes:type>' . ItunesHeader::TYPE_EPISODIC . '</itunes:type>',
            $renderedResult
        );
    }

    public function testingSerialType()
    {
        $renderedResult = ItunesHeader::prepare([
            'type' => ItunesHeader::TYPE_SERIAL,
        ])->render();
        $this->assertStringContainsString(
            '<itunes:type>' . ItunesHeader::TYPE_SERIAL . '</itunes:type>',
            $renderedResult
        );
    }

    public function testingNotExplicit()
    {
        $renderedResult = ItunesHeader::prepare([
            'explicit' => false,
        ])->render();
        $this->assertStringContainsString(
            '<itunes:explicit>false</itunes:explicit>',
            $renderedResult
        );
    }

    public function testingExplicit()
    {
        $renderedResult = ItunesHeader::prepare([
            'explicit' => true,
        ])->render();
        $this->assertStringContainsString(
            '<itunes:explicit>true</itunes:explicit>',
            $renderedResult
        );
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $renderedResult = ItunesHeader::prepare()->render();
        $this->assertEmpty($renderedResult);
    }
}
