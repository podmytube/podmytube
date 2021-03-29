<?php

namespace Tests\Unit;

use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
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
        //Storage::disk('remote')->deleteDirectory($this->destFolder);
        parent::tearDown();
    }

    public function testingFileUpdloadIsOk()
    {
        Storage::disk('remote')->makeDirectory($this->destFolder);
        $localFile = __DIR__ . '/../fixtures/images/sampleVig.jpg';
        $remoteFile = Storage::disk('remote')->path($this->destFolder . '/testVig.jpg');
        $result = SendFileBySFTP::dispatchNow($localFile, $remoteFile, false);
        $visibility = Storage::disk('remote')->getVisibility($this->destFolder);
        $this->assertTrue($result);
        $this->assertTrue(Storage::disk('remote')->exists($remoteFile));
    }

    /** @test */
    public function over_ssh()
    {
        $host = config('filesystems.disks.remote.host');
        $user = config('filesystems.disks.remote.username');
        $privKey = config('filesystems.disks.remote.privateKey');
        $rsa = new RSA;
        $rsa->loadKey($privKey . '.pub');

        $ssh = new SSH2($host, 22);
        dump($ssh->getServerPublicHostKey());
        $key = new RSA();
        $key->loadKey(file_get_contents($privKey));
        if (!$ssh->login($user, $key)) {
            exit('Login Failed');
        }
        dump($ssh->exec('ls -la'));

        /* if ($expected != $ssh->getServerPublicHostKey()) {
            throw new \Exception('Host key verification failed');
        } */
    }
}
