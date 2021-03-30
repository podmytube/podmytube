<?php

namespace App\Modules;

use App\Exceptions\SftpConnectionFailedException;
use App\Exceptions\SftpMakeDirectoryFailureException;
use App\Exceptions\SshConnectionFailedException;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;

class NiceSSH
{
    protected string $host;
    protected string $user;
    protected string $publicKeyFilePath;
    protected int $port;
    protected bool $loadedKey = false;
    protected string $rootPath;

    protected ?\phpseclib\Crypt\RSA $publicKey;
    protected ?\phpseclib\Net\SSH2 $ssh;
    protected ?\phpseclib\Net\SFTP $sftp;

    private function __construct(string $host, string $user, string $publicKeyFilePath, string $rootPath, int $port = 22)
    {
        if (!strlen($host)) {
            throw new InvalidArgumentException("ssh host {$host} is not valid.");
        }
        $this->host = $host;

        if (!strlen($user)) {
            throw new InvalidArgumentException("ssh user {$user} is not valid.");
        }
        $this->user = $user;

        if (!strlen($rootPath)) {
            throw new InvalidArgumentException("ssh root path {$rootPath} is not valid.");
        }
        $this->rootPath = $rootPath;

        if (!strlen($publicKeyFilePath) || !file_exists($publicKeyFilePath)) {
            throw new InvalidArgumentException("ssh public key file path {$publicKeyFilePath} is not valid.");
        }
        $this->publicKeyFilePath = $publicKeyFilePath;
        $this->loadKey();

        $this->port = $port;
    }

    public static function init(string $host, string $user, string $publicKeyFilePath, string $rootPath, int $port = 22)
    {
        return new static($host, $user, $publicKeyFilePath, $rootPath, $port);
    }

    public function loadKey()
    {
        $this->publicKey = new RSA();
        if (!$this->publicKey->loadKey(file_get_contents($this->publicKeyFilePath))) {
            throw new InvalidArgumentException("ssh public key file {$this->publicKeyFilePath} is not a valid one.");
        }
        $this->loadedKey = true;
    }

    public function checkHost()
    {
        $ssh = new SSH2($this->host, 22);
        dump($ssh->getServerPublicHostKey());
    }

    public function sshConnect()
    {
        $this->ssh = new SSH2($this->host, $this->port);
        try {
            if (!$this->ssh->login($this->user, $this->publicKey)) {
                throw new SshConnectionFailedException("SSH Connection with server {$this->user}@{$this->host}:{$this->port} has failed.");
            }
        } catch (Exception $exception) {
            throw new SshConnectionFailedException("Connection with server {$this->user}@{$this->host}:{$this->port} has failed with {$exception->getMessage()}.");
        }
        return $this;
    }

    public function sftpConnect()
    {
        $this->sftp = new SFTP($this->host, $this->port);
        try {
            if (!$this->sftp->login($this->user, $this->publicKey)) {
                throw new SftpConnectionFailedException("SFTP Connection with server {$this->user}@{$this->host}:{$this->port} has failed.");
            }
        } catch (Exception $exception) {
            throw new SftpConnectionFailedException("Connection with server {$this->user}@{$this->host}:{$this->port} has failed with {$exception->getMessage()}.");
        }
        return $this;
    }

    public function loadedKey(): bool
    {
        return $this->loadedKey === true;
    }

    public function rootPathIsValid()
    {
        return $this->isDir($this->rootPath);
    }

    public function isDir($remoteDirectory): bool
    {
        if (!isset($this->sftp)) {
            $this->sftpConnect();
        }
        return $this->sftp->is_dir($remoteDirectory);
    }

    public function isWritable($remoteDirectory): bool
    {
        if (!isset($this->sftp)) {
            $this->sftpConnect();
        }
        return $this->sftp->touch("{$remoteDirectory}/touched");
    }

    public function mkdir($absoluteRemoteDirectory, $mode = -1, $recursive = false): bool
    {
        if (!isset($this->sftp)) {
            $this->sftpConnect();
        }
        $result = $this->sftp->mkdir($absoluteRemoteDirectory, $mode, $recursive);
        if ($result === false) {
            throw new SftpMakeDirectoryFailureException("Folder creation of {$absoluteRemoteDirectory} has failed.");
        }
        Log::debug("folder created : $absoluteRemoteDirectory");
        return true;
    }

    /**
     * @param string $absoluteLocalFilePath  file to be copied from local. Path should be absolute.
     * @param string $relativeRemoteFilePath destination file. Path should be relative to rootPath.
     */
    public function putFile(string $absoluteLocalFilePath, string $relativeRemoteFilePath)
    {
        if (!file_exists($absoluteLocalFilePath)) {
            throw new InvalidArgumentException("{$absoluteLocalFilePath} does not exists.");
        }

        /** getting folder where to put file if any (IE : "<THIS_PART>/file.mp3" ) */
        $relativeFolder = pathinfo($relativeRemoteFilePath, PATHINFO_DIRNAME);

        $absoluteDestFolder = $this->absolutePath($relativeFolder);

        if (!$this->isdir($absoluteDestFolder)) {
            $this->mkdir($absoluteDestFolder, -1, true);
        }

        if (!isset($this->sftp)) {
            $this->sftpConnect();
        }

        $absoluteRemoteFilePath = $this->absolutePath($relativeRemoteFilePath);
        Log::debug("file copied : $absoluteRemoteFilePath");
        return $this->sftp->put($absoluteRemoteFilePath, $absoluteLocalFilePath, SFTP::SOURCE_LOCAL_FILE);
    }

    public function rmDir($folderToDelete)
    {
        if (!isset($this->sftp)) {
            $this->sftpConnect();
        }

        if ($folderToDelete[0] !== '/') {
            $folderToDelete = $this->absolutePath($folderToDelete);
        }

        Log::debug("folder deleted : $folderToDelete");
        return $this->sftp->rmdir($folderToDelete);
    }

    public function absolutePath($relativePath)
    {
        return "{$this->rootPath}/{$relativePath}";
    }
}
