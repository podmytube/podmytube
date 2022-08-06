<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Category;
use App\Podcast\ItunesHeader;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ItunesHeaderTest extends TestCase
{
    use RefreshDatabase;

    /** @var string */
    protected $renderedResult;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedCategories();
    }

    /** @test */
    public function with_all_informations(): void
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
        $this->assertStringContainsString("<itunes:author>{$authorName}</itunes:author>", $this->renderedResult);
        $this->assertStringContainsString("<itunes:title>{$itunesTitle}</itunes:title>", $this->renderedResult);
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:name>{$authorName}</itunes:name>", $this->renderedResult);
        $this->assertStringContainsString("<itunes:email>{$authorEmail}</itunes:email>", $this->renderedResult);
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

    /** @test */
    public function author_name(): void
    {
        $authorName = 'Gérard Choufleur';
        $this->renderedResult = ItunesHeader::prepare(['author' => $authorName])->render();
        $this->assertStringContainsString("<itunes:author>{$authorName}</itunes:author>", $this->renderedResult);
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:name>{$authorName}</itunes:name>", $this->renderedResult);
        $this->assertStringContainsString('</itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $this->renderedResult);

        $this->tagsThatShouldNotBeThere([
            '<itunes:title>',
            '<itunes:category>',
            '<itunes:type>',
            '<language>',
        ]);
    }

    /** @test */
    public function author_email(): void
    {
        $authorEmail = 'gerard@choufleur.com';
        $this->renderedResult = ItunesHeader::prepare(['email' => $authorEmail])->render();
        $this->assertStringContainsString('<itunes:owner>', $this->renderedResult);
        $this->assertStringContainsString("<itunes:email>{$authorEmail}</itunes:email>", $this->renderedResult);
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

    /** @test */
    public function image_url(): void
    {
        $imageUrl = 'https://thumbs.podmytube.com/UCf2ZTuey5uT8hN3ESe-ZCPQ/KjxVJazWLmuLsOdiQ0w0u0iHVTT7jqMCgKlIjCyQ.png';
        $this->renderedResult = ItunesHeader::prepare(['imageUrl' => $imageUrl])->render();
        $this->assertStringContainsString("<itunes:image href=\"{$imageUrl}\" />", $this->renderedResult);
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

    /** @test */
    public function invalid_image_url(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ItunesHeader::prepare(['imageUrl' => 'this is not a valid url'])->render();
    }

    /** @test */
    public function invalid_type(): void
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => 'InvalidType'])->render();
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

    /** @test */
    public function episodic_type(): void
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => ItunesHeader::TYPE_EPISODIC])->render();
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

    /** @test */
    public function serial_type(): void
    {
        $this->renderedResult = ItunesHeader::prepare(['type' => ItunesHeader::TYPE_SERIAL])->render();
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

    /** @test */
    public function explicit(): void
    {
        $this->renderedResult = ItunesHeader::prepare(['explicit' => true])->render();
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

    /** @test */
    public function no_informations_should_render_nothing(): void
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

    /** @test */
    public function check_explicit_is_ok(): void
    {
        $this->assertEquals('true', ItunesHeader::checkExplicit(true));
        $this->assertEquals('true', ItunesHeader::checkExplicit('true'));

        $this->assertEquals('false', ItunesHeader::checkExplicit(false));
        $this->assertEquals('false', ItunesHeader::checkExplicit('false'));

        $this->assertEquals('false', ItunesHeader::checkExplicit('chat'));
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */

    public function tagsThatShouldNotBeThere(array $tagsThatShouldBeMissing): void
    {
        array_map(function ($tagToCheck): void {
            $this->assertStringNotContainsString(
                $tagToCheck,
                $this->renderedResult,
                "Tag {$tagToCheck} should be missing in {$this->renderedResult}."
            );
        }, $tagsThatShouldBeMissing);
    }
}
