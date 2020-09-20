<?php

namespace App\Console\Commands;

use App\Modules\WordpressPosts;
use Illuminate\Console\Command;

class RefreshBlogPosts extends Command
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        WordpressPosts::init()->getPostsFromRemote()->update();
    }
}
