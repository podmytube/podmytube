<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Post;
use App\Modules\ServerRole;
use App\Sitemap\Sitemap;
use App\Sitemap\SitemapNode;
use Illuminate\Console\Command;

/**
 * will update website sitemap.
 */
class UpdateSitemapCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the sitemap';

    protected Sitemap $sitemap;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isDisplay()) {
            $this->warn('This server is not dedicated to display.');

            return 0;
        }
        $this->prologue();

        $this->info('Updating sitemap', 'v');

        $this->sitemap = Sitemap::init();

        // adding standard routes
        $this->addUsualRoutes();

        // adding blog posts
        $this->addPosts();

        // saving
        $this->sitemap->save();

        $this->comment('Sitemap {' . public_path('sitemap.xml') . '} updated with success.', 'v');

        $this->epilogue();

        return Command::SUCCESS;
    }

    protected function addPosts(): void
    {
        Post::query()
            ->where('status', '=', 1)
            ->orderBy('published_at', 'desc')
            ->get()
            ->each(fn (Post $post) => $this->sitemap->addNode(SitemapNode::withSitemapable($post)))
        ;
    }

    protected function addUsualRoutes(): void
    {
        collect([
            'www.index',
            'privacy',
            'about',
            'pricing',
            'terms',
            'faq',
            'post.index',
        ])->each(fn ($routeName) => $this->sitemap->addNode(SitemapNode::withRoute(loc: route($routeName))));
    }
}
