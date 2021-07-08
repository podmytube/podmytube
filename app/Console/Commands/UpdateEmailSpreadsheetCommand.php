<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\GoogleSpreadsheetFactory;
use App\User;
use Illuminate\Console\Command;

/**
 * will update website sitemap.
 */
class UpdateEmailSpreadsheetCommand extends Command
{
    public const USERS_SPREADSHEET_ID = '1kvW6tTjN11hblybVY28o7z7qpjBSwEMySopCluf4gFE';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:spreadsheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the user\'s emails on spreadsheet used with YAMM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating users spreadsheet', 'v');

        /*
         * get all user info
         * email, firstname, lastname
         */
        $headers = [
            ['email', 'firstname', 'lastname'],
        ];

        /** getting users that should receive newsletter */
        $users = User::whoWantNewsletter()->toArray();

        /** formatting as an array */
        $content = array_map(function ($user) {
            return [
                $user['email'],
                $user['firstname'],
                $user['lastname'],
            ];
        }, $users);

        $dataToWrite = array_merge($headers, $content);

        // overwrite user spreasheet
        GoogleSpreadsheetFactory::forSpreadsheetId(self::USERS_SPREADSHEET_ID)
            ->updateRange('A1:C', $dataToWrite)
        ;

        $this->comment('Spreadsheet updated with success.', 'v');
    }
}
