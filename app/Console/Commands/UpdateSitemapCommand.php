<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\ServerRole;
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
     */
    public function handle(): int
    {
        if (!ServerRole::isDisplay()) {
            $this->info('This server is not dedicated to display.', 'v');

            return 0;
        }

        $this->info('Updating sitemap', 'v');
        SitemapGenerator::create('https://www.podmytube.com')->getSitemap()->writeToFile(public_path('sitemap.xml'));
        $this->comment('Sitemap {' . public_path('sitemap.xml') . '} updated with success.', 'v');

        return 0;
    }
}
