<?php

namespace Tests\Unit\Podcast;

use Tests\TestCase;
use App\Podcast\ItunesOwner;
use InvalidArgumentException;

class ItunesOwnerTest extends TestCase
{
    public function testingValidInformationsShouldRenderProperly()
    {
        $result = ItunesOwner::prepare([
            'itunesOwnerName' => 'John doe',
            'itunesOwnerEmail' => 'john.doe@gmail.com'
        ])
            ->render();
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString('<itunes:name>John doe</itunes:name>', $result);
        $this->assertStringContainsString('<itunes:email>john.doe@gmail.com</itunes:email>', $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
    }

    public function testingOnlyOwnerNameShouldRenderProperlyToo()
    {
        $result = ItunesOwner::prepare(['itunesOwnerName' => 'John doe', ])->render();
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString('<itunes:name>John doe</itunes:name>', $result);
        $this->assertStringContainsString('</itunes:owner>', $result);
        $this->assertStringNotContainsString('<itunes:email>', $result);
    }

    public function testingOnlyOwnerEmailShouldRenderProperlyToo()
    {
        $result = ItunesOwner::prepare(['itunesOwnerEmail' => 'john.doe@gmail.com'])->render();
        $this->assertStringContainsString('<itunes:owner>', $result);
        $this->assertStringContainsString('<itunes:email>john.doe@gmail.com</itunes:email>', $result);
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
