<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\GoogleApiAuthFileIsMissingException;
use Google_Client;

class GoogleFactory
{
    public const APPLICATION_NAME = 'Podmytube';

    /** @var \Google_Client */
    protected $client;

    private function __construct()
    {
        $authFile = storage_path(config('app.google_spreadsheet_auth_file'));
        if (!file_exists($authFile)) {
            throw new GoogleApiAuthFileIsMissingException("Config file {$authFile} is missing and required.");
        }
        $this->client = new Google_Client();
        $this->client->setApplicationName(self::APPLICATION_NAME);
        $this->client->setAuthConfig($authFile);
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function client(): Google_Client
    {
        return $this->client;
    }

    public function withScope(string $scope): self
    {
        $this->client->setScopes($scope);

        return $this;
    }
}
