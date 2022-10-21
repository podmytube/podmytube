<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearSessionCommand extends Command
{
    use BaseCommand;

    /** @var string */
    protected $signature = 'clear:session';

    /** @var string */
    protected $description = 'This command will clear session files on local env';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        match (config('session.driver')) {
            'file' => $this->deleteSessionFiles()
        };
        $this->comment('Sessions have been cleared.');

        return Command::SUCCESS;
    }

    protected function deleteSessionFiles(): void
    {
        $files = Storage::disk('sessions')->files();

        array_map(function (string $filepath): void {
            if ($filepath === '.gitignore') {
                return;
            }
            Storage::disk('sessions')->delete($filepath);
        }, $files);
    }
}
