<?php

namespace Tests\Unit\Podcast;

use Tests\TestCase;
use App\Podcast\ItunesOwner;
use InvalidArgumentException;

class ItunesOwnerTest extends TestCase
{
    protected $ownerName = 'John doe';
    protected $ownerEmail = 'john.doe@gmail.com';

    public function testingValidInformationsShouldRenderProperly()
    {
        $result = ItunesOwner::prepare([
            'itunesOwnerName' => $this->ownerName,
            'itunesOwnerEmail' => $this->ownerEmail,
        ])
            ->render();
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString("<itunes:email>{$this->ownerEmail}</itunes:email>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
    }

    public function testingOnlyOwnerNameShouldRenderProperlyToo()
    {
        $result = ItunesOwner::prepare(['itunesOwnerName' => 'John doe', ])->render();
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:name>{$this->ownerName}</itunes:name>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
        $this->assertStringNotContainsString('<itunes:email>', $result);
    }

    public function testingOnlyOwnerEmailShouldRenderProperlyToo()
    {
        $result = ItunesOwner::prepare(['itunesOwnerEmail' => $this->ownerEmail])->render();
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString("<itunes:email>{$this->ownerEmail}</itunes:email>", $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
        $this->assertStringNotContainsString('<itunes:name>', $result);
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $result = ItunesOwner::prepare()->render();
        $this->assertEmpty($result);
    }

    public function testingInvalidEmailShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        ItunesOwner::prepare(['itunesOwnerEmail' => 'Invalid email address']);
    }
}
