<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\GoogleApiAuthFileIsMissingException;
use App\Factories\GoogleFactory;
use Google\Service\Sheets;
use Google_Client;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class GoogleFactoryTest extends TestCase
{
    /** @test */
    public function should_throw_exception_when_auth_file_missing(): void
    {
        Config::set('app.google_spreadsheet_auth_file', 'this-file-does-not-exists');
        $this->expectException(GoogleApiAuthFileIsMissingException::class);
        GoogleFactory::init();
    }

    /** @test */
    public function should_be_good_when_auth_file_here(): void
    {
        $result = GoogleFactory::init()->client();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Google_Client::class, $result);
    }

    /** @test */
    public function set_valid_scope_should_be_good(): void
    {
        $result = GoogleFactory::init()->withScope(Sheets::SPREADSHEETS)->client();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Google_Client::class, $result);
    }
}
