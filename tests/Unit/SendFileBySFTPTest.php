<?php

namespace Tests\Unit;

use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
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

    public function tearDown():void
    {
        Storage::disk('kim1')->deleteDirectory($this->destFolder);
        parent::tearDown();
    }

    public function testingFileUpdloadIsOk()
    {
        $localFile = __DIR__ . '/../fixtures/images/sampleVig.jpg';
        $remoteFile = Storage::disk('kim1')->path($this->destFolder . '/testVig.jpg');
        $result = SendFileBySFTP::dispatchNow($localFile, $remoteFile);
        $this->assertTrue($result);
        $this->assertTrue(Storage::disk('kim1')->exists($remoteFile));
    }
}
