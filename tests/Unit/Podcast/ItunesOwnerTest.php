<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Podcast\ItunesOwner;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ItunesOwnerTest extends TestCase
{
    protected $ownerName = 'John doe';
    protected $ownerEmail = 'john.doe@gmail.com';

    /** @test */
    public function valid_informations_should_render_properly(): void
    {
        $result = ItunesOwner::prepare([
            'itunesOwnerName' => $this->ownerName,
            'itunesOwnerEmail' => $this->ownerEmail,
        ])
            ->render()
        ;
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString("<itunes:email>{$this->ownerEmail}</itunes:email>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
    }

    /** @test */
    public function only_owner_name_should_render_properly_too(): void
    {
        $result = ItunesOwner::prepare(['itunesOwnerName' => 'John doe'])->render();
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
        $this->assertStringNotContainsString('<itunes:email>', $result);
    }

    /** @test */
    public function only_owner_email_should_render_properly_too(): void
    {
        $result = ItunesOwner::prepare(['itunesOwnerEmail' => $this->ownerEmail])->render();
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:email>{$this->ownerEmail}</itunes:email>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
        $this->assertStringNotContainsString('<itunes:name>', $result);
    }

    /** @test */
    public function no_informations_should_render_nothing(): void
    {
        $result = ItunesOwner::prepare()->render();
        $this->assertEmpty($result);
    }

    /** @test */
    public function invalid_email_should_fail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ItunesOwner::prepare(['itunesOwnerEmail' => 'Invalid email address']);
    }
}
