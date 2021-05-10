<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

/**
 * will update website sitemap.
 */
class UpdateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the sitemap';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating sitemap', 'v');
        SitemapGenerator::create('https://www.podmytube.com')->getSitemap()->writeToFile(public_path('sitemap.xml'));
        $this->comment('Sitemap {' . public_path('sitemap.xml') . '} updated with success.', 'v');
    }
}
