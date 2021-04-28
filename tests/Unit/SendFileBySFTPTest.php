<?php

namespace Tests\Unit;

use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $sourceDisk = 'tmp';

        /** creating file to be transferred */
        $fileName = Str::random(4) . '.txt';
        $sourceRelativeFilePath = "chat/$fileName";
        $destinationRelativeFilePath = "tests/foo/{$fileName}";
        Storage::disk($sourceDisk)->put($sourceRelativeFilePath, 'chat');

        /* $remotefolder = pathinfo($destinationRelativeFilePath, PATHINFO_DIRNAME);
        if (!Storage::disk('remote')->exists($remotefolder)) {
            Storage::disk('remote')->makeDirectory($remotefolder);
        } */
        /** transferring */
        Storage::disk('remote')->put(
            $destinationRelativeFilePath,
            Storage::disk($sourceDisk)->get($sourceRelativeFilePath)
        );

        //$result = SendFileBySFTP::dispatchNow(localFile, $remoteFile, false);
        //$this->assertTrue($result);
    }
}
