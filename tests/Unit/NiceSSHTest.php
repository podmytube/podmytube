<?php

namespace Tests\Unit;

use App\Exceptions\SftpConnectionFailedException;
use App\Exceptions\SftpMakeDirectoryFailureException;
use App\Exceptions\SshConnectionFailedException;
use App\Modules\NiceSSH;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Tests\TestCase;

class NiceSSHTest extends TestCase
{
    use WithFaker;

    protected string $host;
    protected string $user;
    protected string $privateKeyPath;
    protected string $rootPath;

    public function setUp():void
    {
        parent::setUp();
        $this->host = config('filesystems.disks.remote.host');
        $this->user = config('filesystems.disks.remote.username');
        $this->privateKeyPath = config('filesystems.disks.remote.privateKey');
        $this->rootPath = config('filesystems.disks.remote.root') . '/tests';
        $this->destFolder = $this->faker->word();
    }

    public function tearDown():void
    {
        NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->rmDir($this->destFolder);
        parent::tearDown();
    }

    /** @test */
    public function no_host_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init('', 'user', 'pubKeyPath', 'rootPath');
    }

    /** @test */
    public function no_user_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init('host', '', 'pubKeyPath', 'rootPath');
    }

    /** @test */
    public function no_public_key_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init('host', 'user', '', 'rootPath');
    }

    /** @test */
    public function unknown_public_key_file_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init('host', 'user', '/this/path/will/never/exists', 'rootPath');
    }

    /** @test */
    public function invalid_public_key_file_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $imageFile = __DIR__ . '/../fixtures/images/sampleVig.jpg';
        NiceSSH::init('host', 'user', $imageFile, 'rootPath');
    }

    /** @test */
    public function invalid_root_path_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init('host', 'user', $this->privateKeyPath, '');
    }

    /** @test */
    public function init_is_fine()
    {
        $niceSSH = NiceSSH::init('lorem', 'ipsum', $this->privateKeyPath, $this->rootPath);
        $this->assertInstanceOf(NiceSSH::class, $niceSSH);
        $this->assertTrue($niceSSH->loadedKey());
    }

    /** @test */
    public function ssh_connect_to_unknown_host_should_fail()
    {
        $this->expectException(SshConnectionFailedException::class);
        NiceSSH::init('non-valid-host', $this->user, $this->privateKeyPath, $this->rootPath)->sshConnect();
    }

    /** @test */
    public function ssh_connect_with_unknown_user_should_fail()
    {
        $this->expectException(SshConnectionFailedException::class);
        NiceSSH::init($this->host, 'non-valid-user', $this->privateKeyPath, $this->rootPath)->sshConnect();
    }

    /** @test */
    public function ssh_connect_with_non_valid_credentials_should_fail()
    {
        $this->expectException(SshConnectionFailedException::class);
        $nonAuthorizedKey = __DIR__ . '/../fixtures/non-authorized-key';
        NiceSSH::init($this->host, $this->user, $nonAuthorizedKey, $this->rootPath)->sshConnect();
    }

    /** @test */
    public function ssh_connect_is_fine()
    {
        $this->assertInstanceOf(NiceSSH::class, NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->sshConnect());
    }

    /** @test */
    public function sftp_connect_should_fail()
    {
        $this->expectException(SftpConnectionFailedException::class);
        NiceSSH::init($this->host, 'non-valid-user', $this->privateKeyPath, $this->rootPath)->sftpConnect();
    }

    /** @test */
    public function sftp_connect_is_fine()
    {
        $this->assertInstanceOf(NiceSSH::class, NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->sftpConnect());
    }

    /** @test */
    public function is_dir_is_ok()
    {
        $niceSSH = NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath);
        $this->assertTrue($niceSSH->isDir('/home/www'));
        $this->assertFalse($niceSSH->isDir('/unknown-directory'));
    }

    /** @test */
    public function is_writable_is_ok()
    {
        $niceSSH = NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath);
        $this->assertTrue($niceSSH->isWritable('/home/www'));
        $this->assertFalse($niceSSH->isWritable('/root'));
    }

    /** @test */
    public function mkdir_is_fine()
    {
        $folderToCreate = '/tmp/foo';
        $niceSSH = NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath);
        $this->assertTrue($niceSSH->mkdir($folderToCreate));
        $niceSSH->rmDir($folderToCreate);
        $this->expectException(SftpMakeDirectoryFailureException::class);
        $niceSSH->mkdir('/root/foo');
    }

    /** @test */
    public function put_file_is_fine()
    {
        $local = __DIR__ . '/../fixtures/images/sampleVig.jpg';
        $remote = "{$this->destFolder}/testVig.jpg";
        $niceSSH = NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath);
        $this->assertTrue($niceSSH->putFile($local, $remote));

        $this->expectException(SftpMakeDirectoryFailureException::class);
        $niceSSH->mkdir('/root/foo');
    }

    /** @test */
    public function put_invalid_file_should_fail_fine()
    {
        $local = '/this/file/does/not/exist.jpg';
        $remote = "{$this->destFolder}/testVig.jpg";
        $this->expectException(InvalidArgumentException::class);
        NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->putFile($local, $remote);
    }

    /** @test */
    public function cannot_delete_write_protected_folder()
    {
        $this->assertFalse(NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->rmDir('read-only'));
    }

    /** @test */
    public function file_exists_is_ok()
    {
        $niceSSH = NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath);
        $this->assertTrue($niceSSH->fileExists('/etc/passwd'));
        $this->assertFalse($niceSSH->fileExists('/this/file/does/not/exist.jpg'));
    }
}
