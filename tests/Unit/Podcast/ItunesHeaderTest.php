<?php

namespace Tests\Unit\Podcast;

use App\Category;
use App\Podcast\ItunesHeader;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ItunesHeaderTest extends TestCase
{
    use RefreshDatabase;

    /** @var string $renderedResult */
    protected $renderedResult;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
    }

    public function testingWithAllInformations()
    {
        $authorName = 'Gérard Choufleur';
        $authorEmail = 'gchoufleur@gmail.com';
        $itunesTitle = 'Mangeur de chamois';
        $this->renderedResult = ItunesHeader::prepare([
            'author' => $authorName,
            'title' => $itunesTitle,
            'imageUrl' => Thumb::defaultUrl(),
            'email' => $authorEmail,
            'category' => Category::bySlug('documentary'),
            'explicit' => 'true',
        ])->render();
        $this->assertStringContainsString("<itunes:author>$authorName</itunes:author>", $this->renderedResult);
        $this->assertStringContainsString("<itunes:title>$itunesTitle</itunes:title>", $this->renderedResult);
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:name>$authorName</itunes:name>", $this->renderedResult);
        $this->assertStringContainsString("<itunes:email>$authorEmail</itunes:email>", $this->renderedResult);
        $this->assertStringContainsString('</itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:explicit>true</itunes:explicit>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:category text="Society &amp; Culture">', $this->renderedResult);
        $this->assertStringContainsString('<itunes:category text="Documentary" />', $this->renderedResult);
        $this->assertStringContainsString('</itunes:category>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:image href="' . Thumb::defaultUrl() . '" />', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingAuthorName()
    {
        $authorName = 'Gérard Choufleur';
        $this->renderedResult = ItunesHeader::prepare(['author' => $authorName])->render();
        $this->assertStringContainsString("<itunes:author>$authorName</itunes:author>", $this->renderedResult);
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:name>$authorName</itunes:name>", $this->renderedResult);
        $this->assertStringContainsString('</itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingAuthorEmail()
    {
        $authorEmail = 'gerard@choufleur.com';
        $this->renderedResult = ItunesHeader::prepare(['email' => $authorEmail, ])->render();
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:email>$authorEmail</itunes:email>", $this->renderedResult);
        $this->assertStringContainsString('</itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:name>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingImageUrl()
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $this->renderedResult = ItunesHeader::prepare(['imageUrl' => $imageUrl, ])->render();
        $this->assertStringContainsString("<itunes:image href=\"$imageUrl\" />", $this->renderedResult);
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingInvalidImageUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        ItunesHeader::prepare(['imageUrl' => 'this is not a valid url', ])->render();
    }

    public function testingInvalidType()
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => 'InvalidType', ])->render();
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingEpisodicType()
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => ItunesHeader::TYPE_EPISODIC, ])->render();
        $this->assertStringContainsString('<itunes:type>' . ItunesHeader::TYPE_EPISODIC . '</itunes:type>', $this->renderedResult);
        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<language>',
        ]);
    }

    public function testingSerialType()
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => ItunesHeader::TYPE_SERIAL, ])->render();
        $this->assertStringContainsString('<itunes:type>' . ItunesHeader::TYPE_SERIAL . '</itunes:type>', $this->renderedResult);
        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<language>',
        ]);
    }

    public function testingExplicit()
    {
        $this->renderedResult = ItunesHeader::prepare(['explicit' => true, ])->render();
        $this->assertStringContainsString('<itunes:explicit>true</itunes:explicit>', $this->renderedResult);
        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $this->renderedResult = ItunesHeader::prepare()->render();
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);
        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:author>',
            '<itunes:owner>',
            '<itunes:name>',
            '<itunes:email>',
            '</itunes:owner>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    public function tagsThatShouldNotBeThere(array $tagsThatShouldBeMissing)
    {
        array_map(function ($tagToCheck) {
            $this->assertStringNotContainsString(
                $tagToCheck,
                $this->renderedResult,
                "Tag {$tagToCheck} should be missing in {$this->renderedResult}."
            );
        }, $tagsThatShouldBeMissing);
    }

    public function testCheckExplicitIsOk()
    {
        $this->assertEquals('true', ItunesHeader::checkExplicit(true));
        $this->assertEquals('true', ItunesHeader::checkExplicit('true'));

        $this->assertEquals('false', ItunesHeader::checkExplicit(false));
        $this->assertEquals('false', ItunesHeader::checkExplicit('false'));

        $this->assertEquals('false', ItunesHeader::checkExplicit('chat'));
    }
}
