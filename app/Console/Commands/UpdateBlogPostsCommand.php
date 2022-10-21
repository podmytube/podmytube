<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Modules\ServerRole;
use App\Modules\WordpressPosts;
use Illuminate\Console\Command;

class UpdateBlogPostsCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will refresh blog posts from wpbackend';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return Command::SUCCESS;
        }
        $this->prologue();

        $this->info('Updating blog posts', 'v');
        $wpPosts = WordpressPosts::init()->getPostsFromRemote()->update();
        $this->comment("Blog posts updated - nb posts added : {$wpPosts->importedPosts()}", 'v');

        $this->epilogue();

        return Command::SUCCESS;
    }
}
