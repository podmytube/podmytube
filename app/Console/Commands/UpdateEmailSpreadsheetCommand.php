<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\GoogleSpreadsheetFactory;
use App\Modules\ServerRole;
use App\User;
use Illuminate\Console\Command;

/**
 * will update website sitemap.
 */
class UpdateEmailSpreadsheetCommand extends Command
{
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
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

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
        GoogleSpreadsheetFactory::forSpreadsheetId(config('app.users_spreadsheet_id'))
            ->updateRange('A1:C', $dataToWrite)
        ;

        $this->comment('Spreadsheet updated with success.', 'v');

        return 0;
    }
}
