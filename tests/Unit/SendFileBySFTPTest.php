<?php

namespace Tests\Unit;

use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendFileBySFTPTest extends TestCase
{
    use WithFaker;

    protected $destFolder;

    public function setUp():void
    {
        parent::setUp();
        $this->destFolder = 'tests/' . $this->faker->word();
    }

    public function testingFileUpdloadIsOk()
    {
        $localFile = __DIR__ . '/../fixtures/images/sampleVig.jpg';
        $remoteFile = $this->destFolder . '/testVig.jpg';
        $result = SendFileBySFTP::dispatchNow($localFile, $remoteFile, false);
        $this->assertTrue($result);
    }
}
