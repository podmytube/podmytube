<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Factories\GoogleSpreadsheetFactory;
use App\Models\User;
use App\Modules\ServerRole;
use Illuminate\Console\Command;

/**
 * will update website sitemap.
 */
class UpdateEmailSpreadsheetCommand extends Command
{
    use BaseCommand;

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

        $this->prologue();
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

        $this->epilogue();

        return Command::SUCCESS;
    }
}
