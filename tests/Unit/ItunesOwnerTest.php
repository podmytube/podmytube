<?php

namespace Tests\Unit;

use App\Podcast\ItunesOwner;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItunesOwnerTest extends TestCase
{
    public function testingValidInformationsShouldRenderProperly()
    {
        $result = ItunesOwner::prepare("John doe", "john.doe@gmail.com")->render();
        $this->assertStringContainsString('<itunes:name>John doe</itunes:name>', $result);
        $this->assertStringContainsString('<itunes:email>john.doe@gmail.com</itunes:email>', $result);
    }

    public function testingPartialInformationsShouldRenderProperlyToo()
    {
        $result = ItunesOwner::prepare("", "john.doe@gmail.com")->render();
        $this->assertStringNotContainsString('<itunes:name>', $result);
        $this->assertStringContainsString('<itunes:email>john.doe@gmail.com</itunes:email>', $result);
    }

    public function testingNoInformationsShouldRenderNothing()
    {
        $result = ItunesOwner::prepare()->render();
        $this->assertEmpty($result);
    }

    public function testingInvalidEmailShouldFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        ItunesOwner::prepare("John doe", "Invalid email address");
    }
}
