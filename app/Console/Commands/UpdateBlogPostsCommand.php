<?php

namespace App\Console\Commands;

use App\Modules\WordpressPosts;
use Illuminate\Console\Command;

class UpdateBlogPostsCommand extends Command
{
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
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating blog posts', 'v');
        $wpPosts = WordpressPosts::init()->getPostsFromRemote()->update();
        $this->comment("Blog posts updated - nb posts added : {$wpPosts->importedPosts()}", 'v');
    }
}
